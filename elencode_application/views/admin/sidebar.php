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
?>
<ul>
  <li><a href="<?=site_url(array('admin'))?>"><?=lang('common_menu_main')?></a></li>
  <li><a href="<?=site_url(array('admin','config','general'))?>"><?=lang('admin_menu_general_option')?></a></li>
  <li><a href="<?=site_url(array('admin','config','universe'))?>"><?=lang('admin_menu_universe_option')?></a>
  <ul>
    <li><a href="<?=site_url(array('admin','config','universe','races'))?>"><?=lang('common_races')?></a></li>
    <li><a href="<?=site_url(array('admin','config','universe','classes'))?>"><?=lang('common_classes')?></a></li>
    <li><a href="<?=site_url(array('admin','config','universe','abilities'))?>"><?=lang('common_abilities')?></a></li>
    <li><a href="<?=site_url(array('admin','config','universe','skills'))?>"><?=lang('common_skills')?></a></li>
    <li><a href="<?=site_url(array('admin','config','universe','var','races-classes'))?>"><?=lang('admin_menu_races_classes')?></a></li>
  </ul></li>
</ul>