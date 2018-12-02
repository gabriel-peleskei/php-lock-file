<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Gabriel Peleskei
	 * Date: 02.12.2018
	 */
	
	use GabrielPeleskei\LockFile\Exception\GeneralException;
	use GabrielPeleskei\LockFile\Exception\IsLockedException;
	use GabrielPeleskei\LockFile\LockFile;
	
	require __DIR__ . '/../vendor/autoload.php';
	
	try {
		declare(ticks=1); // important for the interrupt handling...
		$a = new LockFile(__DIR__ . '/.interrupt-controlled.lock', []);
		$intHandler = $a->getInterruptHandler();
		$intHandler->setSignals(SIGINT, SIGTERM);
		// your could just do:
		// $intHandler = \GabrielPeleskei\InterruptHandler\Handler::getInstance([SIGINT, SIGTERM]);
		$listener = $intHandler->register([SIGINT]);
		// for nicer output
		$listener->name('SIGINT-Signal');
		$a->remove(); // clear before... DON'T DO THIS IN PRODUCTION !!
		$a->start(); // now its locked...
		// do something...
		echo "iterating 10 times with 1 second delay...\n";
		echo "Press CTRL + C for interruption testing...\n";
		// the internal interrupt handler should remove the lock file...
		for($i=1; $i <= 10; $i++) {
			if ( $listener->interrupt) {
				echo "$listener received interrupt...\n";
				echo "Breaking loop and exiting gracefully...\n";
				break;
			}
			echo "$i / 10 ...\n";
			sleep(1);
		}
		echo "Exiting the normal way...\n";
	} catch (IsLockedException $e) {
		echo "Locked: Process is locked!\n";
		exit(1);
	} catch (GeneralException $e) {
		echo "EXCEPTION: {$e->getMessage()} ({$e->getCode()})\n";
		exit(2);
	}
	exit;