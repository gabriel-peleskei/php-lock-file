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
		$locker = new LockFile(__DIR__ . '/.basic.lock', []);
		$locker->remove(); // just to be save, DON'T DO THIS IN PRODUCTION !
		$locker->start(); // throws if lock file exists
		echo "Processing...\n";
		// do whatever..
		// with destructor called,
		// lockfile should be removed at the end...
	} catch (IsLockedException $e) {
		echo "Locked: Process is locked!\n";
		exit(1);
	} catch (GeneralException $e) {
		echo "EXCEPTION: {$e->getMessage()} ({$e->getCode()})\n";
		exit(2);
	}
	exit;