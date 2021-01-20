<?php if($pg_pages>0) {?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#pagination').twbsPagination({
		        totalPages: <?php echo $pg_pages;?>,
		        visiblePages: 7,
		        first: '第一頁',
		        prev : '回上頁',
		        next : '下一頁',
		        last : '最終頁',
		        href: '?pg_page={{number}}&pg_total=<?php echo $pg_total.'&'.$search_query_string;?>'
		    });
		});
	</script>
<?php }?>

	<div style="width:100%; text-align:center;" class="content">
		<ul id="pagination" class="pagination-md"></ul>
	    <div style="padding:10px;">總頁數:<?php echo $pg_pages;?>&nbsp;&nbsp;&nbsp;&nbsp;總筆數:<?php echo $pg_total;?></div>
	</div>