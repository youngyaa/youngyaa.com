<?php

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

/**
 * Some basic utility functions required by the plugin and shared by class
 * 
 */
class JchOptimizeBase
{

        protected $sBodyHtml = '';
        protected $sHeadHtml = '';

        /**
         * Search area used to find js and css files to remove
         * 
         * @return string
         */
        public function getHeadHtml()
        {
                if ($this->sHeadHtml == '')
                {
                        $sHeadRegex = $this->getHeadRegex();

                        if (preg_match($sHeadRegex, $this->sHtml, $aHeadMatches) === FALSE || empty($aHeadMatches))
                        {
                                throw new Exception('An error occured while trying to find the <head> tags in the HTML document. Make sure your template has <head> and </head>');
                        }

                        $this->sHeadHtml = $aHeadMatches[0];
                }

                return $this->sHeadHtml;
        }

        /**
         * Fetches HTML to be sent to browser
         * 
         * @return string
         */
        public function getHtml()
        {
                $sHtml = preg_replace($this->getHeadRegex(), JchOptimizeHelper::cleanReplacement($this->sHeadHtml), $this->sHtml, 1);

                if (is_null($sHtml) || $sHtml == '')
                {
                        throw new Exception('Error occured while trying to get html');
                }

                return $sHtml;
        }

        /**
         * Determines if file requires http protocol to get contents (Not allowed)
         * 
         * @param string $sUrl
         * @return boolean
         */
        public function isHttpAdapterAvailable($sUrl)
        {
                return !(preg_match('#^(?:http|//)#i', $sUrl) && !JchOptimizeUrl::isInternal($sUrl)
                        || $this->isPHPFile($sUrl));
        }

        /**
         * Regex for head search area
         * 
         * @return string
         */
        public function getHeadRegex()
        {
                return '#<head\b[^>]*+>(?><?[^<]*+)*?</head>#i';
        }
        
        /**
         * 
         * @param type $sUrl
         * @return type
         */
        public function isPHPFile($sUrl)
        {
                return preg_match('#\.php|^(?![^?\#]*\.(?:css|js|png|jpe?g|gif|bmp)(?:[?\#]|$)).++#i', $sUrl);
        }

        /**
         * 
         * @return boolean
         */
        public function excludeDeclaration($sType)
        {
                return TRUE;
        }

        /**
         * 
         * @return boolean
         */
        public function runCookieLessDomain()
        {
                return FALSE;
        }

        /**
         * 
         * @return boolean
         */
        public function lazyLoadImages()
        {
                return FALSE;
        }

        /**
         * Regex for body section in Html
         * 
         * @return string
         */
        public function getBodyRegex()
        {
                return '#<body\b[^>]*+>.*</body>#si';
        }

}
