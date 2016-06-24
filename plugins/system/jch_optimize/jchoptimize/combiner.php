<?php

use JchOptimize\JS_Optimize;
use JchOptimize\CSS_Optimize;

/**
 * JCH Optimize - Aggregate and minify external resources for optmized downloads
 * 
 * @author Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2010 Samuel Marshall
 * @license GNU/GPLv3, See LICENSE file
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */
defined('_JCH_EXEC') or die('Restricted access');

class JchOptimizeCombinerBase
{

        /**
         * 
         * @param type $sPath
         * @return boolean
         */
        protected function loadAsync()
        {
                return false;
        }

        /**
         * 
         * @param type $sContent
         * @return type
         */
        protected function replaceImports($sContent)
        {
                return $sContent;
        }

}

/**
 * 
 * 
 */
class JchOptimizeCombiner extends JchOptimizeCombinerBase
{

        public $params            = NULL;
        public $sLnEnd            = '';
        public $sTab              = '';
        public $bBackend          = FALSE;
        public static $bLogErrors = FALSE;
        public $css               = '';
        public $js                = '';
        public $oCssParser;
        protected $oParser;
        protected $current_file = '';

        /**
         * Constructor
         * 
         */
        public function __construct($params, $oParser, $bBackend = FALSE)
        {
                $this->params   = $params;
                $this->oParser  = $oParser;
                $this->bBackend = $bBackend;

                $this->sLnEnd = JchPlatformUtility::lnEnd();
                $this->sTab   = JchPlatformUtility::tab();

                $this->oCssParser = new JchOptimizeCssParser($params, $bBackend);

                self::$bLogErrors = $this->params->get('jsmin_log', 0) ? TRUE : FALSE;
        }

        /**
         * 
         * @return type
         */
        public function getLogParam()
        {
                if (self::$bLogErrors == '')
                {
                        self::$bLogErrors = $this->params->get('log', 0);
                }

                return self::$bLogErrors;
        }

        /**
         * Get aggregated and possibly minified content from js and css files
         *
         * @param array $aUrlArray      Array of urls of css or js files for aggregation
         * @param string $sType         css or js
         * @return string               Aggregated (and possibly minified) contents of files
         */
        public function getContents($aUrlArray, $sType)
        {
                JCH_DEBUG ? JchPlatformProfiler::start('GetContents - ' . $sType, TRUE) : null;

                $oCssParser   = $this->oCssParser;
                $sCriticalCss = '';
                $aSpriteCss   = array();
                $aFontFace    = array();

                $aContentsArray = array();

                foreach ($aUrlArray as $index => $aUrlInnerArray)
                {
                        $sContents = $this->combineFiles($aUrlInnerArray, $sType, $oCssParser);
                        $sContents = $this->prepareContents($sContents);

                        if($sType == 'js')
                        {
                                $sContents .= $this->minifyContent($this->sAsyncContent, 'js', 'async');
                        }
                        
                        $aContentsArray[$index] = $sContents;
                }

                if ($sType == 'css')
                {
                        if ($this->params->get('csg_enable', 0))
                        {
                                try
                                {
                                        $oSpriteGenerator = new JchOptimizeSpriteGenerator($this->params);
                                        $aSpriteCss       = $oSpriteGenerator->getSprite($this->$sType);
                                }
                                catch (Exception $ex)
                                {
                                        JchOptimizeLogger::log($ex->getMessage(), $this->params);
                                        $aSpriteCss = array();
                                }
                        }

                        if ($this->params->get('pro_optimizeCssDelivery_enable', '0'))
                        {
                                if (is_array($aSpriteCss) && !empty($aSpriteCss) && isset($aSpriteCss['needles']) && isset($aSpriteCss['replacements']))
                                {
                                        $this->$sType = str_replace($aSpriteCss['needles'], $aSpriteCss['replacements'], $this->$sType);
                                }

                                $oParser = $this->oParser;
                                $oParser->params->set('pro_InlineScripts', '0');
                                $oParser->params->set('pro_InlineStyles', '0');

                                $sHtml = $oParser->cleanHtml();

                                $aCssContents = $oCssParser->optimizeCssDelivery($this->$sType, $sHtml);
                                $sCriticalCss .= $oCssParser->sortImports($aCssContents['criticalcss']);
                                $aFontFace    = preg_split('#}\K[^@]*+#', $aCssContents['font-face'], -1, PREG_SPLIT_NO_EMPTY);
                        }
                }

                $aContents = array(
                        'filemtime'   => JchPlatformUtility::unixCurrentDate(),
                        'etag'        => md5($this->$sType),
                        'file'        => $aContentsArray,
                        'criticalcss' => $sCriticalCss,
                        'spritecss'   => $aSpriteCss,
                        'font-face'   => $aFontFace
                );

                JCH_DEBUG ? JchPlatformProfiler::stop('GetContents - ' . $sType) : null;

                return $aContents;
        }

        /**
         * Aggregate contents of CSS and JS files
         *
         * @param array $aUrlArray      Array of links of files
         * @param string $sType          CSS or js
         * @return string               Aggregarted contents
         * @throws Exception
         */
        public function combineFiles($aUrlArray, $sType, $oCssParser)
        {
                $sContents = '';
                $iLifetime = (int) $this->params->get('cache_lifetime', '1') * 24 * 60 * 60;

                $this->bAsync    = FALSE;
                $this->sAsyncContent = '';

                $oFileRetriever = JchOptimizeFileRetriever::getInstance();

                foreach ($aUrlArray as $aUrl)
                {
                        $sUrl = $this->prepareFileUrl($aUrl, $sType);

                        JCH_DEBUG ? JchPlatformProfiler::start('CombineFile - ' . $sUrl) : null;

                        if ($sType == 'js')
                        {
                                $sJsContents = $this->handleAsyncUrls($aUrl);
                                
                                if($sJsContents != '')
                                {
                                        $sContents .= $sJsContents;
                                        
                                        continue;
                                }
                        }

                        if (isset($aUrl['id']) && $aUrl['id'] != '')
                        {
                                if(isset($aUrl['url']))
                                {
                                       $this->current_file = $aUrl['url'];
                                }
                                
                                $function = array($this, 'cacheContent');
                                $args     = array($aUrl, $sType, $oFileRetriever, $oCssParser, TRUE);

                                $sCachedContent = JchPlatformCache::getCallbackCache($aUrl['id'], $iLifetime, $function, $args);

                                $this->$sType .= $sCachedContent;
                                
                                $sContents .= $this->addCommentedUrl($sType, $aUrl) . '[[JCH_' . $aUrl['id'] . ']]' .
                                                $this->sLnEnd . 'DELIMITER';
                        }
                        else
                        {
                                $sContent = $this->cacheContent($aUrl, $sType, $oFileRetriever, $oCssParser, FALSE);
                                $sContents .= $this->addCommentedUrl($sType, $aUrl) . $sContent . '|"LINE_END"|';
                        }

                        JCH_DEBUG ? JchPlatformProfiler::stop('CombineFile - ' . $sUrl, TRUE) : null;
                }

                if ($this->bAsync)
                {
                        $sContents = $this->getLoadScript() . $sContents;
                }

                return $sContents;
        }

        /**
         * 
         * @param type $aUrl
         * @param type $sType
         * @param type $oFileRetriever
         * @return type
         * @throws Exception
         */
        public function cacheContent($aUrl, $sType, $oFileRetriever, $oCssParser, $bPrepare)
        {
                $sContent = '';

                if (isset($aUrl['url']))
                {
                        $sPath = JchOptimizeHelper::getFilePath($aUrl['url']);
                        $sContent .= $oFileRetriever->getFileContents($sPath);
                }
                else
                {
                                $sContent .= $aUrl['content'];
                }

                if ($sType == 'css')
                {
                        $sImportContent = preg_replace('#@import\s(?:url\()?[\'"]([^\'"]+)[\'"](?:\))?#', '@import url($1)', $sContent);

                        if (is_null($sImportContent))
                        {
                                JchOptimizeLogger::log(sprintf('There was an error when trying to find "@imports" in the CSS file: %s',
                                                                                             $aUrl['url']), $this->params);

                                $sImportContent = $sContent;
                        }

                        $sContent = $sImportContent;
                        unset($sImportContent);

                        $sContent = $oCssParser->addRightBrace($sContent);

                        $oCssParser->aUrl = $aUrl;
                        
                        $sContent = $oCssParser->correctUrl($sContent, $aUrl);
                        $sContent = $this->replaceImports($sContent, $aUrl);
                        $sContent = $oCssParser->handleMediaQueries($sContent, $aUrl['media']);
                        

                        if (function_exists('mb_convert_encoding'))
                        {
                                $sContent = mb_convert_encoding($sContent, 'utf-8');
                        }
                }

                if ($sType == 'js' && trim($sContent) != '')
                {
                        if ($this->params->get('try_catch', '1'))
                        {
                                $sContent = $this->addErrorHandler($sContent, $aUrl);
                        }
                        else
                        {
                                $sContent = $this->addSemiColon($sContent, $aUrl);
                        }
                }

                if ($bPrepare)
                {
                        $sContent = $this->minifyContent($sContent, $sType, $aUrl);
                        $sContent = $this->prepareContents($sContent);
                }

                return $sContent;
        }

        /**
         * 
         * @param type $sUrl
         */
        protected function handleAsyncUrls($aUrl)
        {
                $sJsContents = '';

                if (isset($aUrl['url']))
                {
                        if ($this->loadAsync($aUrl['url']))
                        {
                                $this->sAsyncContent .= $this->sLnEnd . '});';
                                $this->bAsync    = TRUE;

                                $sJsContents = $this->addCommentedUrl('js', $aUrl['url']) .
                                        'loadScript("' . $aUrl['url'] . '", function(){  DELIMITER'; 
                        }
                }

                return $sJsContents;
        }

        /**
         * 
         * @param type $sContent
         * @param type $sUrl
         */
        protected function minifyContent($sContent, $sType, $aUrl)
        {
                if ($this->params->get($sType . '_minify', 0))
                {
                        $sUrl = $this->prepareFileUrl($aUrl, $sType);

                        $sMinifiedContent = trim($sType == 'css' ? CSS_Optimize::optimize($sContent) : JS_Optimize::optimize($sContent));

                        if (is_null($sMinifiedContent) || $sMinifiedContent == '')
                        {
                                JchOptimizeLogger::log(sprintf('Error occurred trying to minify: %s', $sUrl), $this->params);
                                $sMinifiedContent = $sContent;
                        }

                        return $sMinifiedContent;
                }

                return $sContent;
        }

        /**
         * 
         * @param type $aUrl
         * @param type $sType
         * @return type
         */
        public function prepareFileUrl($aUrl, $sType)
        {
                $sUrl = isset($aUrl['url']) ?
                        JchOptimizeAdmin::prepareFileValues($aUrl['url'], '', 40) :
                        ($sType == 'css' ? 'Style' : 'Script') . ' Declaration';

                return $sUrl;
        }

        /**
         * 
         * @param type $sType
         * @param type $sUrl
         * @return string
         */
        protected function addCommentedUrl($sType, $sUrl)
        {
                $sComment = '';

                if ($this->params->get('debug', '1'))
                {
                        if (is_array($sUrl))
                        {
                                $sUrl = isset($sUrl['url']) ? $sUrl['url'] : (($sType == 'js' ? 'script' : 'style') . ' declaration');
                        }

                        $sComment = '|"COMMENT_START ' . $sUrl . ' COMMENT_END"|';
                }

                return $sComment;
        }

        /**
         * Add semi-colon to end of js files if non exists;
         * 
         * @param string $sContent
         * @return string
         */
        public function addErrorHandler($sContent, $aUrl)
        {
                if (trim($sContent) != '')
                {
                        $sContent = 'try {' . $this->sLnEnd . $sContent . $this->sLnEnd . '} catch (e) {' . $this->sLnEnd;
                        $sContent .= 'console.error(\'Error in ';
                        $sContent .= isset($aUrl['url']) ? 'file:' . $aUrl['url'] : 'script declaration';
                        $sContent .= '; Error:\' + e.message);' . $this->sLnEnd . '};';
                }

                return $sContent;
        }

        /**
         * Add semi-colon to end of js files if non exists;
         * 
         * @param string $sContent
         * @return string
         */
        public function addSemiColon($sContent)
        {
                $sContent = rtrim($sContent);

                if (substr($sContent, -1) != ';' && !preg_match('#\|"COMMENT_START File[^"]+not found COMMENT_END"\|#', $sContent))
                {
                        $sContent = $sContent . ';';
                }

                return $sContent;
        }

        /**
         * Remove placeholders from aggregated file for caching
         * 
         * @param string $sContents       Aggregated file contents
         * @param string $sType           js or css
         * @return string
         */
        public function prepareContents($sContents, $test = FALSE)
        {
                $sContents = str_replace(
                        array(
                        '|"COMMENT_START',
                        '|"COMMENT_IMPORT_START',
                        'COMMENT_END"|',
                        'DELIMITER',
                        '|"LINE_END"|'
                        ),
                        array(
                        $this->sLnEnd . '/***! ',
                        $this->sLnEnd . $this->sLnEnd . '/***! @import url',
                        ' !***/' . $this->sLnEnd . $this->sLnEnd,
                        ($test) ? 'DELIMITER' : '',
                        $this->sLnEnd
                        ), trim($sContents));


                return $sContents;
        }

        
        
}
