<?php
defined('TYPO3_MODE') or die();


$localExtConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['tt_address']);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('options.saveDocNew.tt_address = 1');

if($localExtConf['activatePiBase'] === 1) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43(
        'tt_address',
        'Classes/Controller/LegacyPluginController.php',
        '_pi1',
        'list_type',
        true
    );
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \TYPO3\TtAddress\Hooks\DataHandler\BackwardsCompatibilityNameFormat::class;

/* ===========================================================================
  Custom cache, done with the caching framework
=========================================================================== */
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_ttaddress_category'])) {
  $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_ttaddress_category'] = array();
}

/* ===========================================================================
  Hooks
=========================================================================== */
if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('realurl')) {
  $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration']['tt_address'] =
    'TYPO3\\TtAddress\\Hooks\\RealUrlAutoConfiguration->addTtAddressConfig';
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . 'tt_address' . '/Configuration/TSconfig/NewContentElementWizard.ts">');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
  "TYPO3." . 'tt_address',
  'ListView',
  array (
    'Address' => 'list,show'
  ),
  array (
    'Address' => 'list'
  ),
  \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
);

// Register evaluations for TCA
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals']['TYPO3\\TtAddress\\Evaluation\\TelephoneEvaluation'] = '';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals']['TYPO3\\TtAddress\\Evaluation\\LatitudeEvaluation'] = '';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals']['TYPO3\\TtAddress\\Evaluation\\LongitudeEvaluation'] = '';


// Update scripts
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['tt_address_group'] = \TYPO3\TtAddress\Updates\AddressGroupToSysCategory::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['tt_address_image'] = \TYPO3\TtAddress\Updates\ImageToFileReference::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['tt_address_legacyplugintyposcript'] = \TYPO3\TtAddress\Updates\TypoScriptTemplateLocation::class;

// Register icon
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class)->registerIcon(
    'tt-address-plugin',
    \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
    ['source' => 'EXT:tt_address/Resources/Public/Icons/ContentElementWizard.gif']
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
    mod.wizards.newContentElement.wizardItems.plugins {
        elements.tx_ttaddress_pi1 {
            iconIdentifier = tt-address-plugin
            title = LLL:EXT:tt_address/Resources/Private/Language/locallang_pi1.xlf:pi1_title
            description = LLL:EXT:tt_address/Resources/Private/Language/locallang_pi1.xlf:pi1_plus_wiz_description
            tt_content_defValues {
                CType = list
                list_type = tt_address_pi1
            }
        }
        show :=addToList(tx_ttaddress_pi1)
    }
');
