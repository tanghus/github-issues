<?php
/**
 * Copyright (c) 2014 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

\OCP\App::registerAdmin('issues', 'settings');
\OCP\Util::addscript('issues', 'loader');
