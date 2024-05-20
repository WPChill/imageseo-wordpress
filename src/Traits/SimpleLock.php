<?php

namespace ImageSeoWP\Traits;

trait SimpleLock
{
	/**
	 * Attempts to acquire a lock.
	 *
	 * @param string $lockName The name of the lock.
	 * @param int $expiration The expiration of the lock in seconds.
	 * @return bool True if the lock was acquired, false otherwise.
	 */
	public function acquireLock($lockName, $expiration = 10)
	{
		// Check if the lock already exists
		if (get_transient($lockName)) {
			return false;  // Lock is already present
		}
		// Set the lock
		set_transient($lockName, true, $expiration);
		return true;
	}

	/**
	 * Releases a lock.
	 *
	 * @param string $lockName The name of the lock to release.
	 */
	public function releaseLock($lockName)
	{
		delete_transient($lockName);
	}
}
