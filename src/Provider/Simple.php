<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Gabriel Peleskei
	 * Date: 25.11.2018
	 */
	
	namespace GabrielPeleskei\LockFile\Provider;
	
	use GabrielPeleskei\LockFile\Exception\GeneralException;
	
	class Simple implements ProviderInterface {
		protected $_sLockFilePath;
		
		/**
		 * ProviderInterface constructor.
		 * @param string $sLockFilePath
		 */
		public function __construct($sLockFilePath) {
			$this->_sLockFilePath = $sLockFilePath;
		}
		
		/**
		 * @param array $aAdditionalData
		 * @throws GeneralException if file is not writable
		 * @uses file_put_contents()
		 * @uses sprintf()
		 */
		public function create(array $aAdditionalData = []) {
			if ( !file_put_contents($this->_sLockFilePath, date('c'))) {
				throw new GeneralException(
					sprintf('Lock file [%s] could not be written', $this->_sLockFilePath), 11);
			}
		}
		
		/**
		 * @return bool true on success
		 * @uses unlink()
		 */
		public function remove() {
			return @unlink($this->_sLockFilePath);
		}
		
		/**
		 * @return bool true if is locked
		 * @uses file_exists()
		 */
		public function isLocked() {
			return file_exists($this->_sLockFilePath);
		}
	}