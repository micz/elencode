<? if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
* Copyright 2009 Elencode
* This file is part of Elencode.
* Elencode is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
* Elencode is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Elencode; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
* The latest source code for Elencode is available at https://github.com/micz/elencode
* Contact the Dev Team at dev [at] elenbar [dot] it
*
*/
echo doctype('xhtml1-trans');
?>
<html>
<head>
<title><?=lang('admin_panel_title')?> :: <?=$sitename?><?=$sectiontitle?></title>
<?=meta('Content-type','text/html; charset=utf-8','equiv');?>
<link rel="stylesheet" type="text/css" href="<?= base_url()?>graphic/css/admin.css" media="screen" />
<script type="text/javascript" src="<?=base_url()?>js/jquery.js"></script>
<script type="text/javascript" src="<?=base_url()?>js/admin.js"></script>
</head>
<body>
<div id="wrapper">
<h1><?=lang('admin_panel_title')?> :: <?=$sitename?><?=$sectiontitle?></h1>
<div id="left">
<?$this->load->view('admin/sidebar')?>
</div><div id="main">
<?$this->load->view($main_content)?>
</div>
<div id="footer">
<?$this->load->view('footer')?>
<br /><?=lang('admin_footer_rendered','{elapsed_time}')?>
</div>
</div>
</body>
</html>