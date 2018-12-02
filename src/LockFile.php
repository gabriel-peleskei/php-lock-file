<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Gabriel Peleskei
	 * Date: 25.11.2018
	 */
	
	namespace GabrielPeleskei\LockFile;
	
	use GabrielPeleskei\InterruptHandler\Handler;
	use GabrielPeleskei\LockFile\Exception\GeneralException;
	use GabrielPeleskei\LockFile\Exception\IsLockedException;
	use GabrielPeleskei\LockFile\Provider\ProviderInterface;
	use GabrielPeleskei\LockFile\Provider\Simple;
	use function pcntl_signal;
	use const SIG_DFL;
	use const SIGHUP;
	use const SIGINT;
	use const SIGTERM;
	use const SIGUSR1;
	
	class LockFile {
		/** @var bool  */
		protected $_blClearable = false;
		/** @var ProviderInterface|string  */
		protected $_provider;
		/**
		 * @var Handler
		 */
		protected $_intHandler;
		
		/**
		 * LockFile constructor.
		 * @param string|ProviderInterface $pathOrProvider
		 * @param array $interrupts
		 * @throws GeneralException if wrong param type given
		 */
		public function __construct($pathOrProvider, array $interrupts = [SIGINT, SIGUSR1, SIGHUP, SIGTERM]) {
			if ($pathOrProvider instanceof ProviderInterface) {
				$this->_provider = $pathOrProvider;
			} else if (is_string($pathOrProvider)) {
				$this->_provider = new Simple($pathOrProvider);
			} else {
				throw new GeneralException('$pathOrProvider must be a string or instance of ProviderInterface', 1);
			}
			$this->_intHandler = Handler::getInstance($interrupts);
			if ( !empty($interrupts)) {
				$this->_intHandler->register($interrupts,[$this, 'handleInterrupt']);
			}
		}
		
		/**
		 * @param $sig
		 */
		public function handleInterrupt($sig) {
			$this->__destruct();
			pcntl_signal($sig, SIG_DFL);
			posix_kill(posix_getpid(), $sig);
		}
		
		/**
		 * @return Handler|static
		 */
		public function getInterruptHandler() {
			return $this->_intHandler;
		}
		
		/**
		 * @param bool|null $blClearable if set to true, lock is removed with instance destruction. return current value on null
		 * @return bool current state
		 */
		public function clearable($blClearable = null) {
			if ( null !== $blClearable) {
				$this->_blClearable = (bool) $blClearable;
			}
			return $this->_blClearable;
		}
		
		/**
		 * @return bool
		 */
		public function isLocked() {
			return $this->_provider->isLocked();
		}
		
		/**
		 * @param array $aAdditionalData additional data to save in lock file
		 */
		public function create(array $aAdditionalData = []) {
			$this->_provider->create($aAdditionalData);
		}
		
		/**
		 * @return bool on success
		 */
		public function remove() {
			return $this->_provider->remove();
		}
		
		/**
		 * @param array $aAdditionalData additional data to save in lock file
		 * @throws IsLockedException
		 * @uses LockFile::isLocked()
		 * @uses LockFile::create()
		 * @uses LockFile::clearable()
		 */
		public function start(array $aAdditionalData = []) {
			if ($this->isLocked()) {
				throw new IsLockedException('Lock active', 2);
			}
			$this->create($aAdditionalData);
			$this->clearable(true);
		}
		
		/**
		 * removes lock file ...
		 */
		public function __destruct() {
			if ($this->_blClearable) {
				$this->_provider->remove();
			}
		}
	}