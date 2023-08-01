<?php
/**
 * JL Content Fields Filter
 *
 * @version 	@version@
 * @author		Joomline
 * @copyright  (C) 2017-2023 Arkadiy Sedelnikov, Sergey Tolkachyov, Joomline. All rights reserved.
 * @license 	GNU General Public License version 2 or later; see	LICENSE.txt
 */

// No direct access
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

defined( '_JEXEC' ) or die;

$app = Factory::getApplication();
if ( $app->isClient('site') ) {
	Session::checkToken( 'get' ) or die( Text::_( 'JINVALID_TOKEN' ) );
}
//HTMLHelper::_( 'bootstrap.tooltip' );
//HTMLHelper::_( 'behavior.framework' );
$function = $app->getInput()->getCmd( 'function', 'jSelectArticle' );
$listOrder = $this->escape( $this->state->get( 'list.ordering' ) );
$listDirn = $this->escape( $this->state->get( 'list.direction' ) );
?>
<form action="<?php echo Route::_( 'index.php?option=com_jlcontentfieldsfilter&view=items&layout=modal&tmpl=component&function=' . $function . '&' . JSession::getFormToken() . '=1' ); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">
	<fieldset class="filter clearfix">
		<div class="btn-toolbar">
			<div class="btn-group pull-left">
				<label for="filter_search">
					<?php echo Text::_( 'JSEARCH_FILTER_LABEL' ); ?>
				</label>
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape( $this->state->get( 'filter.search' ) ); ?>" size="30" title="<?php echo Text::_( 'COM_CONTENT_FILTER_SEARCH_DESC' ); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" data-placement="bottom" title="<?php echo Text::_( 'JSEARCH_FILTER_SUBMIT' ); ?>">
					<i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip" data-placement="bottom" title="<?php echo Text::_( 'JSEARCH_FILTER_CLEAR' ); ?>" onclick="document.id('filter_search').value='';this.form.submit();">
					<i class="icon-remove"></i></button>
			</div>
			<div class="clearfix"></div>
		</div>
		<hr class="hr-condensed" />
		<div class="filters">
			<select name="filter_published" class="input-medium" onchange="this.form.submit()">
				<option value=""><?php echo Text::_( 'JOPTION_SELECT_PUBLISHED' ); ?></option>
				<?php echo HTMLHelper::_( 'select.options', HTMLHelper::_( 'jgrid.publishedOptions' ), 'value', 'text', $this->state->get( 'filter.published' ), true ); ?>
			</select>
		</div>
	</fieldset>
	<table class="table table-striped table-condensed">
		<thead>
		<tr>
			<th class="title">
				<?php echo HTMLHelper::_( 'grid.sort', 'JGLOBAL_TITLE', 'title', $listDirn, $listOrder ); ?>
			</th>
			<th width="10%" class="hidden-phone">
				<?php echo HTMLHelper::_( 'grid.sort', 'JAUTHOR', 'created_by', $listDirn, $listOrder ); ?>
			</th>
			<th width="5%" class="center nowrap">
				<?php echo HTMLHelper::_( 'grid.sort', 'JDATE', 'created', $listDirn, $listOrder ); ?>
			</th>
			<th width="1%" class="center nowrap">
				<?php echo HTMLHelper::_( 'grid.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder ); ?>
			</th>
		</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="15"><?php echo $this->pagination->getListFooter(); ?></td>
		</tr>
		</tfoot>
		<tbody>
		<?php foreach ( $this->items as $i => $item ) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="has-context">
					<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape( $function ); ?>('<?php echo $item->id; ?>', '<?php echo $this->escape( addslashes( $item->title ) ); ?>');">
						<?php echo $this->escape( $item->title ); ?></a>
				</td>
				<td class="small hidden-phone">
					<?php echo $this->escape( $item->created_by ); ?>
				</td>
				<td class="center">
					<?php echo HTMLHelper::_( 'date', $item->created, Text::_( 'DATE_FORMAT_LC4' ) ); ?>
				</td>
				<td class="center">
					<?php echo (int)$item->id; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo HTMLHelper::_( 'form.token' ); ?>
	</div>
</form>