<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_jlcontentfieldsfilter
 *
 * @version     @version@
 * @author      Joomline
 * @copyright   (C) 2017-2025 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Jlcontentfieldsfilter\Administrator\Helper;

use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Helper class for jlcontentfieldsfilter component.
 *
 * @since  1.0.0
 */
class JlcontentfieldsfilterHelper
{
    /**
     * Add submenu.
     *
     * @param string $vName The view name.
     *
     * @return void
     *
     * @since   1.0.0
     */
    public static function addSubmenu($vName)
    {
        Sidebar::addEntry(
            Text::_('ITEM_SUBMENU'),
            'index.php?option=com_jlcontentfieldsfilter&view=items',
            $vName == 'items'
        );
    }

    /**
     * Get the available actions for the current user.
     *
     * @return \stdClass An object with the available actions.
     *
     * @since   1.0.0
     */
    public static function getActions()
    {
        $user      = Factory::getApplication()->getIdentity();
        $result    = new \stdClass();
        $assetName = 'com_jlcontentfieldsfilter';
        $actions   = Access::getActionsFromFile(
            JPATH_ADMINISTRATOR . '/components/com_jlcontentfieldsfilter/access.xml',
            '/access/section[@name="component"]/'
        );
        foreach ($actions as $action) {
            $result->{$action->name} = $user->authorise($action->name, $assetName);
        }
        return $result;
    }

    /**
     * Create a filter string from filter data array.
     *
     * @param array $filter Filter data array
     * @param bool $safe Whether to URL encode values
     *
     * @return string Filter string
     *
     * @since   1.0.0
     */
    public static function createFilterString($filter, $safe=true)
    {
        ksort($filter);
        $data = [];
        foreach ($filter as $key => $item) {
            if (\is_array($item)) {
                // Special handling for range fields (from/to)
                if (isset($item['from']) || isset($item['to'])) {
                    // Range field: fieldid[from]=val1&fieldid[to]=val2
                    if (!empty($item['from'])) {
                        $val = $safe ? urlencode($item['from']) : $item['from'];
                        $data[] = $key . '[from]=' . $val;
                    }
                    if (!empty($item['to'])) {
                        $val = $safe ? urlencode($item['to']) : $item['to'];
                        $data[] = $key . '[to]=' . $val;
                    }
                } else {
                    // Regular multi-value field: fieldid=val1,val2,val3
                    $val = [];
                    ksort($item);
                    foreach ($item as $k => $v) {
                        if (!empty($v)) {
                            $val[] = $safe ? urlencode($v) : $v;
                        }
                    }
                    if (\count($val)) {
                        $data[] = $key . '=' . implode(',', $val);
                    }
                }
            } else {
                // Security: Fix operator precedence - parentheses needed for ternary operator
                $data[] = $key . '=' . ($safe ? urlencode($item) : $item);
            }
        }

        $data = implode('&', $data);
        return $data;
    }

    /**
     * Create an MD5 hash from a string.
     *
     * @param string $string Input string
     *
     * @return string MD5 hash
     *
     * @since   1.0.0
     */
    public static function createHash($string)
    {
        return md5($string);
    }

    /**
     * Create meta data object for a filter.
     *
     * @param int $catid Category ID
     * @param array $filterData Filter data array
     *
     * @return \stdClass Meta data object with title, description and keywords
     *
     * @since   1.0.0
     */
    public static function createMeta($catid, $filterData)
    {
        // Load component language files from component directory (not global language folder)
        // Component language files are in: administrator/components/com_jlcontentfieldsfilter/language/
        $lang = Factory::getApplication()->getLanguage();
        $lang->load('com_jlcontentfieldsfilter', JPATH_ADMINISTRATOR . '/components/com_jlcontentfieldsfilter');
        
        $object                = new \stdClass();
        $object->meta_title    = '';
        $object->meta_desc     = '';
        $object->meta_keywords = '';

        if (!$catid) {
            return $object;
        }
        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select($db->quoteName('title'))
            ->from('#__categories')
            ->where('id = '.(int)$catid)
        ;
        $catName = $db->setQuery($query, 0, 1)->loadResult();

        $query->clear()->select($db->quoteName(['id', 'title', 'type', 'fieldparams']))
            ->from($db->quoteName('#__fields'))
            ->where('(' . $db->quoteName('context') . ' = '.$db->quote('com_content.article').' OR ' . $db->quoteName('context') . ' = '.$db->quote('com_contact.contact').')')
        ;
        $result = $db->setQuery($query)->loadObjectList();

        if (!\count($result)) {
            return $object;
        }

        $fields = [];

        foreach ($result as $field) {
            $values = false;

            $fieldparams = json_decode($field->fieldparams, true);
            if (isset($fieldparams['options']) && \is_array($fieldparams['options']) && \count($fieldparams['options'])) {
                $values = [];
                foreach ($fieldparams['options'] as $option) {
                    $key = $option['value'];
                    if (is_numeric($key)) {
                        $key = (int)$key;
                    }
                    $values[$key] = Text::_($option['name']);
                }
            }

            $fields[$field->id] = [
                'id'     => $field->id,
                'name'   => Text::_($field->title),
                'values' => $values,
            ];
        }

        $titles = $desc = $keyvords = [];
        foreach ($filterData as $key => $f) {
            if (!isset($fields[$key]) || empty($f)) {
                continue;
            }
            $fname = $fields[$key]['name'];
            
            if (\is_array($f)) {
                // Handle range fields (from/to)
                if (isset($f['from']) || isset($f['to'])) {
                    $rangeValues = [];
                    if (!empty($f['from'])) {
                        $rangeValues[] = $f['from'];
                    }
                    if (!empty($f['to'])) {
                        $rangeValues[] = $f['to'];
                    }
                    
                    if (\count($rangeValues)) {
                        $fValue = implode('-', $rangeValues);
                        // For meta description, use more natural language
                        if (\count($rangeValues) === 2) {
                            $fValueDesc = Text::_('COM_JLCONTENTFIELDSFILTER_META_FROM') . ' ' . $rangeValues[0] . ' ' . Text::_('COM_JLCONTENTFIELDSFILTER_META_TO') . ' ' . $rangeValues[1];
                        } elseif (!empty($f['from'])) {
                            $fValueDesc = Text::_('COM_JLCONTENTFIELDSFILTER_META_FROM') . ' ' . $rangeValues[0];
                        } else {
                            $fValueDesc = Text::_('COM_JLCONTENTFIELDSFILTER_META_UP_TO') . ' ' . $rangeValues[0];
                        }
                        
                        // Title: compact format "Field: 500-1000"
                        $titles[] = $fname . ': ' . $fValue;
                        // Description: natural format "Field from 500 to 1000"
                        $desc[] = $fname . ' ' . $fValueDesc;
                        // Keywords: just the values
                        $keyvords[] = $fValue;
                    }
                } else {
                    // Handle regular multi-value fields (checkboxes, list)
                    $fValues = [];
                    foreach ($f as $fk => $fv) {
                        if (empty($fv)) {
                            continue;
                        }
                        if (is_numeric($fv)) {
                            $fv = (int)$fv;
                        }
                        if ($fields[$key]['values'] === false) {
                            $fValues[] = $fv;
                        } elseif (isset($fields[$key]['values'][$fv])) {
                            $fValues[] = $fields[$key]['values'][$fv];
                        } else {
                            $fValues[] = $fv;
                        }
                    }
                    
                    if (\count($fValues)) {
                        $fValue = implode(', ', $fValues);
                        $titles[] = $fname . ': ' . $fValue;
                        $desc[] = $fname . ': ' . $fValue;
                        $keyvords[] = $fValue;
                    }
                }
            } else {
                // Handle single value fields (radio, text)
                if (is_numeric($f)) {
                    $f = (int)$f;
                }
                if ($fields[$key]['values'] === false) {
                    $fValue = $f;
                } elseif (isset($fields[$key]['values'][$f])) {
                    $fValue = $fields[$key]['values'][$f];
                } else {
                    $fValue = $f;
                }
                
                if (!empty($fValue)) {
                    $titles[] = $fname . ': ' . $fValue;
                    $desc[] = $fname . ': ' . $fValue;
                    $keyvords[] = $fValue;
                }
            }
        }
        $object->catid         = $catid;
        $object->filter        = JlcontentfieldsfilterHelper::createFilterString($filterData);
        $object->filter_hash   = JlcontentfieldsfilterHelper::createHash($object->filter);
        
        // Build SEO-optimized meta tags
        // Title: Compact format for better display in search results
        // Format: "Category - Filter1: Value1; Filter2: Value2"
        if (\count($titles) > 0) {
            $metaTitle = $catName . ' - ' . implode('; ', $titles);
        } else {
            $metaTitle = $catName;
        }
        
        // Description: Natural language format with category context
        // Format: "Browse Category filtered by Filter1: Value1, Filter2: Value2"
        if (\count($desc) > 0) {
            $metaDesc = Text::_('COM_JLCONTENTFIELDSFILTER_META_BROWSE') . ' ' . $catName . ' ' . Text::_('COM_JLCONTENTFIELDSFILTER_META_FILTERED_BY') . ' ' . implode(', ', $desc);
        } else {
            $metaDesc = $catName;
        }
        
        // Keywords: Clean list of filter values only
        $metaKeywords = implode(', ', $keyvords);
        
        // Limit meta fields to database column sizes and SEO best practices
        // Title: 255 chars (database limit), Description: 1000 chars, Keywords: 500 chars
        $object->meta_title    = mb_substr($metaTitle, 0, 255);
        $object->meta_desc     = mb_substr($metaDesc, 0, 1000);
        $object->meta_keywords = mb_substr($metaKeywords, 0, 500);
        $object->state         = 1;

        $db->insertObject('#__jlcontentfieldsfilter_data', $object, 'id');

        return $object;
    }
}
