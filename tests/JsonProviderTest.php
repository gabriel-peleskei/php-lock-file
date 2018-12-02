<?php
	/**
	 * Created by IntelliJ IDEA.
	 * User: Gabriel Peleskei
	 * Date: 25.11.2018
	 */
	
	namespace GabrielPeleskei\LockFile\Test;
	
	use GabrielPeleskei\LockFile\Exception\GeneralException;
	use GabrielPeleskei\LockFile\Exception\IsLockedException;
	use GabrielPeleskei\LockFile\LockFile;
	use GabrielPeleskei\LockFile\Provider\JSON;
	use GabrielPeleskei\LockFile\Provider\Simple;
	use PHPUnit\Framework\TestCase;
	
	class JsonProviderTest extends TestCase {
		const TEST_FILE = '.json-simple.lock';
		/** @var LockFile */
		protected $_source;
		protected $_path;
		
		protected function rm() {
			@unlink($this->_path);
		}
		
		protected function setUp() {
			parent::setUp();
			$this->_path = __DIR__ . DIRECTORY_SEPARATOR . self::TEST_FILE;
			$this->_source = new LockFile(new JSON($this->_path));
			$this->rm();
		}
		
		
		public function testCreation() {
			$this->assertFileNotExists($this->_path, 'Lock file should not exist!');
		}
		
		/**
		 * @expectedException \GabrielPeleskei\LockFile\Exception\GeneralException
		 */
		public function testCreateExceptionNumber() {
			new LockFile(123);
		}
		
		/**
		 * @expectedException \GabrielPeleskei\LockFile\Exception\GeneralException
		 */
		public function testCreateExceptionBool() {
			new LockFile(true);
		}
		
		/**
		 * @expectedException \GabrielPeleskei\LockFile\Exception\GeneralException
		 */
		public function testCreateExceptionArray() {
			new LockFile(['adasd']);
		}
		
		
		/**
		 * @expectedException \GabrielPeleskei\LockFile\Exception\GeneralException
		 */
		public function testCreateExceptionOtherClass() {
			new LockFile(new GeneralException());
		}
		
		
		public function testBasicLogic() {
			$this->assertEquals(false, $this->_source->isLocked(), 'Lock exists, but should not');
			$this->_source->create();
			$this->assertEquals(true, $this->_source->isLocked(), 'Lock file is missing');
			$this->assertEquals(true, $this->_source->remove(), 'Lock file has not been removed');
			$this->assertEquals(false, $this->_source->isLocked(), 'Lock file exists, but should not');
			$this->assertEquals(false, $this->_source->clearable(),'Lock file is cleareable, but should not be');
			
		}
		
		/**
		 * @expectedException  \GabrielPeleskei\LockFile\Exception\IsLockedException
		 */
		public function testLockException() {
			$this->_source->create();
			$this->_source->start();
		}
		
		public function testStartMechanism() {
			$this->_source->remove();
			$this->_source->start();
			$this->assertEquals(true, $this->_source->isLocked(), 'Lock file does not exist');
			$this->assertEquals(true, $this->_source->clearable(), 'Lock is not clearable');
		}
		
		protected function tearDown() {
			parent::tearDown();
			$this->_source = null;
			$this->rm();
		}
		
		
	}