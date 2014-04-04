<?php
/**
 * @author Thomas Tanghus
 * @copyright 2013-2014 Thomas Tanghus (thomas@tanghus.net)
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace OCA\Issues\Utils;

class Config {

	public function __construct($storage, $file) {
		$this->storage = $storage;
		$this->file = $file;
	}

	/**
	 * Read any configured Github client configuration for the current user
	 *
	 * @return array|false Array containing 'user' and 'password' keys or false on error
	 */
	public function read() {
		if (!$this->storage->is_file($this->file)) {
			\OCP\Util::writeLog(
				'issues', __METHOD__ . ': Config file does not exists: ' . $this->file,
				\OCP\Util::DEBUG
			);
			return false;
		}

		$config = json_decode($this->storage->file_get_contents($this->file), true);

		if (!isset($config['password']) || !isset($config['login'])) {
			\OCP\Util::writeLog(
				'issues', __METHOD__
				. ': Config file exists, but either login or password is missing',
				\OCP\Util::DEBUG
			);
			return false;
		}

		$config['password'] = $this->decryptPassword($config['password']);

		return $config;
	}

	/**
	 * Add/update Github credentials.
	 *
	 * @param string $login
	 * @param string $password
	 * @throws \Exception On error
	 */
	public function write($login, $password) {

		$config = array(
			'login' => $login,
			'password' => $this->encryptPassword($password)
		);

		$options = 0;
		if (defined('JSON_PRETTY_PRINT')) {
			// only for PHP >= 5.4
			$options = JSON_PRETTY_PRINT;
		}
		if (!$this->storage->file_put_contents($this->file, json_encode($config, $options))) {
		//	$this->storage->chmod($this->file, 0600);
		//} else {
			throw new \Exception(
				'Web server does not have permission to write to '
				. $this->file
			);
		}
	}

	/**
	 * Remove credentials.
	 *
	 * @throws \Exception On error
	 */
	public function remove() {
		if (!$this->storage->unlink($this->file)) {
			throw new \Exception(
				'Web server does not have permission to delete '
				. $this->file
			);
		}
	}

	/**
	 * Encrypt a single password
	 *
	 * @param string $password plain text password
	 * @return encrypted password
	 */
	private function encryptPassword($password) {
		$cipher = self::getCipher();
		$iv = \OCP\Util::generateRandomBytes(16);
		$cipher->setIV($iv);
		return base64_encode($iv . $cipher->encrypt($password));
	}

	/**
	 * Decrypts a single password
	 * @param string $encryptedPassword encrypted password
	 * @return plain text password
	 */
	private function decryptPassword($encryptedPassword) {
		$cipher = self::getCipher();
		$binaryPassword = base64_decode($encryptedPassword);
		$iv = substr($binaryPassword, 0, 16);
		$cipher->setIV($iv);
		$binaryPassword = substr($binaryPassword, 16);
		return $cipher->decrypt($binaryPassword);
	}

	/**
	 * Returns the encryption cipher
	 */
	private function getCipher() {
		if (!class_exists('Crypt_AES', false)) {
			throw new \Exception(
				'phpseclib is not installed.'
			);
		}
		$cipher = new \Crypt_AES(CRYPT_AES_MODE_CBC);
		$cipher->setKey(\OCP\Config::getSystemValue('passwordsalt'));
		return $cipher;
	}
}