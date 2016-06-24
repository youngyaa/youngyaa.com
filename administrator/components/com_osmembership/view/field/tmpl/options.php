<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     OSMembership
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
?>
<table cellspacing="3" cellpadding="3" width="100%">
    <?php
        $optionsPerLine = 3;
        for ($i = 0 , $n = count($this->options) ; $i < $n ; $i++)
        {
            $value = $this->options[$i] ;
            if ($i % $optionsPerLine == 0) {
            ?>
                <tr>
            <?php
            }
            ?>
            <td>
                <input class="inputbox" value="<?php echo $value; ?>" type="checkbox" name="depend_on_options[]"><?php echo $value;?>
            </td>
            <?php
            if (($i+1) % $optionsPerLine == 0)
            {
                ?>
                </tr>
            <?php
            }
        }
        if ($i % $optionsPerLine != 0)
        {
            $colspan = $optionsPerLine - $i % $optionsPerLine ;
            ?>
            <td colspan="<?php echo $colspan; ?>">&nbsp;</td>
            </tr>
        <?php
        }
    ?>
</table>
