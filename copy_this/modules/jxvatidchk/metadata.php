<?php

/**
 * Metadata version
 */
$sMetadataVersion = '1.1';
 
/**
 * Module information
 */
$aModule = array(
    'id'           => 'jxvatidchk',
    'title'        => 'jxVatIdChk - Online VAT ID Check',
    'description'  => array(
                        'de'=>'Online-&Uuml;berpr&uuml;fung der Ust-ID.',
                        'en'=>'Online Check of VAT ID.'
                        ),
    'thumbnail'    => 'jxvatidchk.png',
    'version'      => '0.2',
    'author'       => 'Joachim Barthel',
    'url'          => 'https://github.com/job963/jxVatIdChk',
    'email'        => 'jobarthel@gmail.com',
    'extend'       => array(
        'oxuser' => 'jxvatidchk/application/controllers/admin/jxvatidchk'
                        ),
    'files'        => array(
                        ),
    'templates'    => array(
                        ),
    'settings' => array(
                        )
    );

?>
