<?php

/*
 *    This file is part of the module jxVatIdChk for OXID eShop Community Edition.
 *
 *    The module OxProbs for OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    The module OxProbs for OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      https://github.com/job963/jxVatIdChk
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @copyright (C) Joachim Barthel 2012-2013
 *
 */
 
class jxvatidchk extends oxUser
{
    public function jxCheckVatId()
    {
        $sVatId = $this->oxuser__oxustid->value;
        if ( empty($sVatId) )
            return'';

        $sVatId = str_replace(array(' ', '.', '-', ',', ', '), '', trim($sVatId));
        $sCountryCode = substr($sVatId, 0, 2);
        $sVatNo = substr($sVatId, 2);
        $oClient = new SoapClient("http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl");
        $oViewConf = oxNew( 'oxViewConfig' );
        $sModuleURL = $oViewConf->getModuleUrl("jxVatIdChk");
        
        $oLang = oxLang::getInstance();
        $sMsgInvalidID  = $oLang->translateString( 'JXVATIDCHK_INVALID' );
        $sMsgServiceErr = $oLang->translateString( 'JXVATIDCHK_SERVICEERROR' );
        $sMsgNoConnect  = $oLang->translateString( 'JXVATIDCHK_NOCONNECTION' );

        if($oClient){
            $aParams = array('countryCode' => $sCountryCode, 'vatNumber' => $sVatNo);
            $sReturn = '';
            try{
                $ret = $oClient->checkVat($aParams);
                if($ret->valid == true){
                    // VAT-ID is valid
                    $sReturn = '<img src="'.$sModuleURL.'views/admin/img/check-ok.png" style="position:relative;top:4px;" title="';

                    foreach($ret as $key=>$prop){
                        $sReturn .= $key . ': ' . $prop . chr(13);
                    }
                    $sReturn .= '" />';
                } else {
                    // VAT-ID is NOT valid/found
                    $sReturn = '<img src="'.$sModuleURL.'views/admin/img/check-invalid.png" style="position:relative;top:4px;" title="'.$sMsgInvalidID.'" /> ' . $sMsgInvalidID;
                }

            } catch(SoapFault $e) {
                //echo 'Error, see message: '.$e->faultstring;
                $aKnownErrors = array('INVALID_INPUT', 'SERVICE_UNAVAILABLE', 'MS_UNAVAILABLE', 'TIMEOUT', 'SERVER_BUSY');
                if ( in_array($e->faultstring,$aKnownErrors) ) {
                    $sKeyShort = 'JXVATIDCHK_SERVICEERROR_' . $e->faultstring;
                    $sKeyLong = 'JXVATIDCHK_SERVICEERROR_' . $e->faultstring . '_EXPLAIN';
                    $sReturn = '<img src="'.$sModuleURL.'views/admin/img/check-unknown.png" style="position:relative;top:4px;" title="'.$sMsgServiceErr.': ' . $oLang->translateString($sKeyLong) . '" /> ' . $oLang->translateString($sKeyShort);
                } else
                    $sReturn = '<img src="'.$sModuleURL.'views/admin/img/check-unknown.png" style="position:relative;top:4px;" title="'.$sMsgServiceErr.': ' . $e->faultstring . '" /> ';
            }
        } else {
            // Connection to host not possible, server down?
            $sReturn = '<img src="'.$sModuleURL.'views/admin/img/check-unknown.png" style="position:relative;top:4px;" title="'.$sMsgNoConnect.'" /> ' . $sMsgNoConnect;
        }

            return $sReturn;
    }
}