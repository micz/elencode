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
//We're extending the base CI language library
class EL_Language extends CI_Language {

  function __construct()
  {
    parent::CI_Language();
  }

  function line($args = null)
	{
    if(is_null($args))
			return false;
		if(!is_array($args))$args=func_get_args();
    $line_id=array_shift($args);
		$line = ($line_id == '' || !isset($this->language[$line_id])) ? $line_id : $this->language[$line_id];
		return @vsprintf($line,$args);
	}
}
?>