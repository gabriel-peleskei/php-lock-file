# php-lock-file

Mechanism to create/remove and test a lock file.
This allows to run a php script only once (as single instance).

Basic Usage
-----------
```php
use GabrielPeleskei\LockFile\Exception\GeneralException;
use GabrielPeleskei\LockFile\Exception\IsLockedException;
use GabrielPeleskei\LockFile\LockFile;

try {
    $locker = new LockFile(__DIR__ . '/.basic.lock', []);
    $locker->start(); // throws if lock file exists
    echo "Processing...\n";
    // do whatever..
    // with destructor called,
    // lockfile should be removed at the end...
} catch (IsLockedException $e) {
    echo "Locked: Process is locked!\n";
    exit(1);
} catch (GeneralException $e) {
    // possible write permission problems...
    echo "EXCEPTION: {$e->getMessage()} ({$e->getCode()})\n";
    exit(2);
}
exit;

```
For use cases with interruptions, look into the /examples folder