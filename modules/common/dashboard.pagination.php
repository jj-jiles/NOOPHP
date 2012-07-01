<?
global $paginate;
$paginate->init( $paginate_vars );
?>


<div class="tabs-pagination">
<? if ( $paginate->pages > 1 ) : ?>
	<? $paginate->first_previous_anchors(); ?>
	<span class="pagination-pages">
	<? $paginate->page_number_anchors(); ?>
	</span>
	<? $paginate->last_next_anchors(); ?>
<? else : ?>
	&nbsp;
<? endif; ?>
</div>
