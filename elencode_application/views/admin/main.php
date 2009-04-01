<? if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
* Copyright 2009 Elencode
* This file is part of Elencode.
* Elencode is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
* Elencode is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Vanilla; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* The latest source code for Elencode is available at https://github.com/micz/elencode
* Contact Mic at m [at] micz [dot] it
*
*/
?>

<html>
<head>
<title>Admin Panel :: Elencode</title>
<link rel="stylesheet" type="text/css" href="<?= base_url()?>graphic/css/admin.css" media="screen" />
</head>
<body>

<h1>Welcome <?= $userdata->Username.' '?>to CodeIgniter!</h1>

<p>Config test=<?= $test_config?></p>

<p>Config test=<?= var_dump($test_uniconf)?></p>

<p>Uniopt test=<?= var_dump($test_uniopt)?></p>

<p>If you would like to edit this page you'll find it located at:</p>
<code>system/application/views/welcome_message.php</code>

<p>The corresponding controller for this page is found at:</p>
<code>system/application/controllers/welcome.php</code>

<p>If you are exploring CodeIgniter for the very first time, you should start by reading the <a href="user_guide/">User Guide</a>.</p>


<p><br />Page rendered in {elapsed_time} seconds</p>

</body>
</html>