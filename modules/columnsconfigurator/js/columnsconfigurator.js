/*
 * Custom code goes here.
 * A template should always ship with an empty custom.js
 */
 
function ColumnsConfig(){
	if (typeof belvg_column_value_hp == "number"){
		var columnClass_hp = 'col-' + belvg_column_value_hp;
		var columnClass_cp = 'col-' + belvg_column_value_cp;		
	}
	$('#index .products .product-miniature').addClass(columnClass_hp);
	$('#category .products .product-miniature').addClass(columnClass_cp);
}
