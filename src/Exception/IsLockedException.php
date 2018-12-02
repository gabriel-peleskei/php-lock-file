<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Gabriel Peleskei
	 * Date: 25.11.2018
	 */
	
	namespace GabrielPeleskei\LockFile\Exception;
	
	/**
	 * Class IsLockedException
	 *
	 * Should be thrown when lock file exists and process should not be started.
	 *
	 * @package GabrielPeleskeiockfile\Exception
	 */
	class IsLockedException extends GeneralException {}