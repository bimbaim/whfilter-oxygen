<?php

class OxywhFilter extends OxyEl {


	public $js_added = false;
	
    function init() {
        // Do some initial things here.
    }

    function afterInit() {
        // Do things after init, like remove apply params button and remove the add button.
        //$this->removeApplyParamsButton();
        // $this->removeAddButton();
    }

    function name() {
        return 'Oxywhello Filter';
    }
    
    function slug() {
        return "oxy-whello-filter-element";
    }

    function icon() {
        // Path to icon here.
    }

    function button_place() {
        // return "interactive";
    }

    function button_priority() {
        // return 9;
    }
    
    function render($options, $defaults, $content) {
        // Output here.
        
        		if( isset($_GET['oxygen_iframe']) || defined('OXY_ELEMENTS_API_AJAX') ) {
					$class = ' postfilter-content-toggle';
				}		
				$post_type = (isset( $options['oxywhello_post_type'] )  ? $options['oxywhello_post_type'] : 'post' );
				$filterby = (isset( $options['oxywhello_filterby'] )  ? $options['oxywhello_filterby'] : 'category' );
				$tax_slug = (isset( $options['oxywhello_tax_slug'] )  ? $options['oxywhello_tax_slug'] : '' );
				$post_per_page = (isset( $options['oxywhello_repeater_posts_per_page'] )  ? $options['oxywhello_repeater_posts_per_page'] : '12' );
				$show_count =  (isset( $options['oxywhello_repeater_show_count'] )  ? $options['oxywhello_repeater_show_count'] : 'no' );
				
				if($filterby == 'taxonomy'){
		
					$taxonomy = $tax_slug;
					
				}else{
					
					$taxonomy = $filterby;
					
				}
				$taxonomies = get_terms( array(
					'taxonomy' => $taxonomy,
					'hide_empty' => false
				) );
				

				if( $show_count == 'yes'){
						$count_posts = wp_count_posts($post_type);
						if ( $count_posts ) {
								$published_posts = '('.$count_posts->publish.')';
							}
						
				}					
									
							
				

        ?>
			<div class='oxywh-filter-container'>
				<div class="oxywh-filter-lists">
					<input type="radio"  id="all"  name="oxywh-radio" value="all" class="oxywh-radio-item">
					<label for="all">All<span class="oxywh-count"><?php echo  $published_posts; ?></span></label><br>
					<?php
						foreach( $taxonomies as $category ) {
										if( $show_count == 'yes'){
													$term = get_term( $category->term_id, $filterby  ); 
													$count =  '('.$term->count.')';
										}
							?>
							<input type="radio"  id="<?php echo strtolower(esc_attr( $category->slug ))?>"  name="oxywh-radio" value="<?php echo strtolower(esc_attr( $category->slug ))?>" class="oxywh-radio-item oxywh-filter-item">
							<label for="<?php echo strtolower(esc_attr( $category->slug ))?>"><?php  echo esc_html( $category->name );  ?><span class="oxywh-count"><?php echo  $count; ?></span></label><br>
						<?php } ?>
				</div>
			</div>
			<script type="text/javascript">
			jQuery(document).ready( function($) {
					var container = "<?php echo '.'.$options['oxywhello_repeater_class_container']; ?>";
					var itemSelector  = "<?php echo '.'.$options['oxywhello_repeater_class_item']; ?>";
					var postPerPage = "<?php echo $post_per_page; ?>";
					jQuery('.oxywh-span').each( function() {
									jQuery(this).closest(itemSelector).addClass(jQuery(this).text().toLowerCase());
					  })
					  
					
					var $checkboxes = $('.oxywh-filter-item');
					var $container = $(container).isotope({ itemSelector: itemSelector });
					//Ascending order
					var responsiveIsotope = [ [480, 4] , [720, 6] ];
					var itemsPerPageDefault = postPerPage;
					var itemsPerPage = defineItemsPerPage();
					var currentNumberPages = 1;
					var currentPage = 1;
					var currentFilter = '*';
					var filterAttribute = 'data-filter';
					var filterValue = "";
					var pageAttribute = 'data-page';
					var pagerClass = 'isotope-pager';
					
					  // update items based on current filters    
						function changeFilter(selector) { $container.isotope({ filter: selector }); }
					
						//grab all checked filters and goto page on fresh isotope output
						function goToPage(n) {
							currentPage = n;
							var selector = itemSelector;
							var exclusives = [];
								// for each box checked, add its value and push to array
								$checkboxes.each(function (i, elem) {
									if (elem.checked) {
										selector += ( currentFilter != '*' ) ? '.'+elem.value : '';
										exclusives.push(selector);
									}
								});
								// smash all values back together for 'and' filtering
								filterValue = exclusives.length ? exclusives.join('') : '*';
								
								// add page number to the string of filters
								var wordPage = currentPage.toString();
								filterValue += ('.'+wordPage);
						   
							changeFilter(filterValue);
						}
					
						// determine page breaks based on window width and preset values
						function defineItemsPerPage() {
							var pages = itemsPerPageDefault;
					
							for( var i = 0; i < responsiveIsotope.length; i++ ) {
								if( $(window).width() <= responsiveIsotope[i][0] ) {
									pages = responsiveIsotope[i][1];
									break;
								}
							}
							return pages;
						}
						
						function setPagination() {
					
							var SettingsPagesOnItems = function(){
								var itemsLength = $container.children(itemSelector).length;
								var pages = Math.ceil(itemsLength / itemsPerPage);
								var item = 1;
								var page = 1;
								var selector = itemSelector;
								var exclusives = [];
									// for each box checked, add its value and push to array
									$checkboxes.each(function (i, elem) {
										if (elem.checked) {
											selector += ( currentFilter != '*' ) ? '.'+elem.value : '';
											exclusives.push(selector);
										}
									});
									// smash all values back together for 'and' filtering
									filterValue = exclusives.length ? exclusives.join('') : '*';
									// find each child element with current filter values
									$container.children(filterValue).each(function(){
										// increment page if a new one is needed
										if( item > itemsPerPage ) {
											page++;
											item = 1;
										}
										// add page number to element as a class
										wordPage = page.toString();
										
										var classes = $(this).attr('class').split(' ');
										var lastClass = classes[classes.length-1];
										// last class shorter than 4 will be a page number, if so, grab and replace
										if(lastClass.length < 4){
											$(this).removeClass();
											classes.pop();
											classes.push(wordPage);
											classes = classes.join(' ');
											$(this).addClass(classes);
										} else {
											// if there was no page number, add it
										   $(this).addClass(wordPage); 
										}
										item++;
									});
								currentNumberPages = page;
							}();
					
							// create page number navigation
							var CreatePagers = function() {
					
								var $isotopePager = ( $('.'+pagerClass).length == 0 ) ? $('<div class="'+pagerClass+'"></div>') : $('.'+pagerClass);
					
								$isotopePager.html('');
								if(currentNumberPages > 1){
								  for( var i = 0; i < currentNumberPages; i++ ) {
									  var $pager = $('<a href="javascript:void(0);" class="pager" '+pageAttribute+'="'+(i+1)+'"></a>');
										  $pager.html(i+1);
				
										  $pager.click(function(){
											  var page = $(this).eq(0).attr(pageAttribute);
											  goToPage(page);
										  });
									  $pager.appendTo($isotopePager);
								  }
								}
								$container.after($isotopePager);
							}();
						}
						// remove checks from all boxes and refilter
						function clearAll(){
							$checkboxes.each(function (i, elem) {
								if (elem.checked) {
									elem.checked = null;
								}
							});
						   currentFilter = '*';
						   setPagination();
						   goToPage(1);
						}
					
						setPagination();
						goToPage(1);
					
						//event handlers
						$checkboxes.change(function(){
							var filter = $(this).attr(filterAttribute);
							currentFilter = filter;
							setPagination();
							goToPage(1);
						});
						
						$('#all').click(function(){clearAll()});
					
						$(window).resize(function(){
							itemsPerPage = defineItemsPerPage();
							setPagination();
							goToPage(1);
						});

			});
			</script>
        <?php
    }

    function controls() {
		
		 // We can create control sections:
        $controlSection = $this->addControlSection("section_slug", __("Custom settings"), "assets/icon.png", $this);
        
		//Custom 
		
		$controlSection->addOptionControl(
			array(
                "type" => 'dropdown',
                "name" => 'Post Type',
                "slug" => 'oxywhello_post_type',
            )
		)->setValue(array('post', 'custom-post-type'));
		
		$controlSection->addOptionControl(
			array(
                "type" => 'textfield',
                "name" => 'Custom Post Type Slug',
                "slug" => 'oxywhello_cpt_slug',
                "condition" => 'oxywhello_post_type=custom-post-type',
            )
		);
		
		//Filter by
        $controlSection->addOptionControl(
			array(
                "type" => 'dropdown',
                "name" => 'Filter by',
                "slug" => 'oxywhello_filterby',
            )
		)->setValue(array('category', 'post_tag', 'taxonomy'));
		
		$controlSection->addOptionControl(
			array(
                "type" => 'textfield',
                "name" => 'Taxonomy Slug',
                "slug" => 'oxywhello_tax_slug',
                "condition" => 'oxywhello_filterby=taxonomy',
            )
		);
		
		$controlSection->addOptionControl(
			array(
                "type" => 'textfield',
                "name" => 'Post Type Slug',
                "slug" => 'oxywhello_post_tag_slug',
                "condition" => 'oxywhello_filterby=post_tag',
            )
		);
		
		$controlSection->addOptionControl(
			array(
                "type" => 'textfield', // types: textfield, dropdown, checkbox, buttons-list, measurebox, slider-measurebox, colorpicker, icon_finder, mediaurl
				"name" => 'Class Repeater Container',
				"slug" => 'oxywhello_repeater_class_container'
            )
        );
        
       $controlSection->addOptionControl(
			array(
                "type" => 'textfield', // types: textfield, dropdown, checkbox, buttons-list, measurebox, slider-measurebox, colorpicker, icon_finder, mediaurl
				"name" => 'Class Repeater Item',
				"slug" => 'oxywhello_repeater_class_item'
            )
        );

       	$controlSection->addOptionControl(
			array(
                "type" => 'textfield', // types: textfield, dropdown, checkbox, buttons-list, measurebox, slider-measurebox, colorpicker, icon_finder, mediaurl
				"name" => 'Posts Per Page',
				"slug" => 'oxywhello_repeater_posts_per_page'
            )
        );
        


        
        $count = $controlSection->addOptionControl(
			array(
                "type" => 'buttons-list', // types: textfield, dropdown, checkbox, buttons-list, measurebox, slider-measurebox, colorpicker, icon_finder, mediaurl
				"name" => 'Show Count Post',
				"slug" => 'oxywhello_repeater_show_count'
            )
        );
        
        $count ->setValue(
            array(
                 'no' => 'No',
                 'yes' => 'Yes'
             )
         );
         
        $count->setDefaultValue('no');
         
        $count->whitelist();

        $count->rebuildElementOnChange();
        
        //typography
		$styling = $controlSection->typographySection(
             __("Lists"),
             '.oxywh-filter-lists',
             $this
         );
        //flex
        $styling->flex(
			'.oxywh-filter-lists',
             $this
        );



    }

    function defaultCSS() {

        return file_get_contents(__DIR__.'/'.basename(__FILE__, '.php').'.css');
 
    }
    
}

new OxywhFilter();
