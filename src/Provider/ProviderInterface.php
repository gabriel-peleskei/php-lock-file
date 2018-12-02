<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Gabriel Peleskei
	 * Date: 25.11.2018
	 */
	namespace GabrielPeleskei\LockFile\Provider;
	
	use GabrielPeleskei\LockFile\Exception\GeneralException;
	use GabrielPeleskei\LockFile\Exception\IsLockedException;
	
	interface ProviderInterface {
		/**
		 * ProviderInterface constructor.
		 * @param string $sLockFilePath
		 */
		public function __construct($sLockFilePath);
		/**
		 * @param array $aAdditionalData
		 * @throws GeneralException if file is not writable
		 * @throws IsLockedException if is locked
		 */
		public function create(array $aAdditionalData = []);
		
		/**
		 * @return bool true on success
		 */
		public function remove();
		
		/**
		 * @return bool true if is locked
		 */
		public function isLocked();
	}