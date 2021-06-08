<?php

if( ! defined('ABSPATH') )	exit;

// Database Migration for Area Management - 07 Jan 2019

if( ! class_exists("Ph_Mc_Db_Migration") ) {
	class Ph_Mc_Db_Migration {

		public function __construct() {
			$db_migrated = get_option( "ph_multicarrier_db_migrated", false );
			if( ! $db_migrated ) {
				$this->area_management = get_option( "woocommerce_wf_multi_carrier_shipping_area_settings");
				$this->db_migration_required_check();
			}
		}

		private function db_migration_required_check() {
			$area_management = $this->area_management;
			if( ! empty($this->area_management['area_matrix']) ) {
				foreach( $this->area_management['area_matrix'] as &$area ) {
					if( ! empty($area['state_list']) ) {
						foreach( $area['state_list'] as &$states ) {
							switch($states) {
								case	"IE:CARLOW"		:	$states = "IE:CW";
															break;
								case	"IE:CAVAN"		:	$states = "IE:CN";
															break;
								case	"IE:CLARE"		:	$states = "IE:CE";
															break;
								case	"IE:CORK"		:	$states = "IE:CO";
															break;
								case	"IE:DONEGAL"	:	$states = "IE:DL";
															break;
								case	"IE:DUBLIN"		:	$states = "IE:D";
															break;
								case	"IE:GALWAY"		:	$states = "IE:G";
															break;
								case	"IE:KERRY"		:	$states = "IE:KY";
															break;
								case	"IE:KILDARE"	:	$states = "IE:KE";
															break;
								case	"IE:KILKENNY"	:	$states = "IE:KK";
															break;
								case	"IE:LAOIS"		:	$states = "IE:LS";
															break;
								case	"IE:LEITRIM"	:	$states = "IE:LM";
															break;
								case	"IE:LIMERICK"	:	$states = "IE:LK";
															break;
								case	"IE:LONGFORD"	:	$states = "IE:LD";
															break;
								case	"IE:LOUTH"		:	$states = "IE:LH";
															break;
								case	"IE:MAYO"		:	$states = "IE:MO";
															break;
								case	"IE:MONAGHAM"	:	$states = "IE:MN";
															break;
								case	"IE:OFFALY"		:	$states = "IE:OY";
															break;
								case	"IE:ROSCOMMON"	:	$states = "IE:RN";
															break;
								case	"IE:SLIGO"		:	$states = "IE:SO";
															break;
								case	"IE:TRIPPERARY"	:	$states = "IE:TA";
															break;
								case	"IE:WTERFORD"	:	$states = "IE:WD";
															break;
								case	"IE:WESTMEATH"	:	$states = "IE:WH";
															break;
								case	"IE:WEXFORD"	:	$states = "IE:WX";
															break;
								case	"IE:WVICKLOW"	:	$states = "IE:WW";
															break;
							}
						}
					}
				}
			}

			if( $area_management != $this->area_management ) {
				update_option( "woocommerce_wf_multi_carrier_shipping_area_settings", $this->area_management );
				update_option( "ph_multicarrier_db_migrated", true );
			}
		}

	}
}