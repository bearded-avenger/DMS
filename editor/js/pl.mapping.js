!function ($) {

	$.plMapping = {
	
       	getCurrentMap: function() {
			
			var that = this
			,	map = {}

			$('.pl-region').each( function(regionIndex, o) {

				var region = $(this).data('region')
				, 	areaConfig = []

				$(this).find('.pl-area').each( function(areaIndex, o2) {

					var area = $(this)
					,	areaContent	= []
					, 	areaName = area.data('name') || ''
					,	areaClass = area.data('class') || ''
					, 	areaID = area.attr('id') || ''
					, 	areaSet = {}

					$(this).find('.pl-section.level1').each( function(sectionIndex, o3) {

						var section = $(this)
						,	sectionsTemplate = section.data('template') || ''
						
						if( sectionsTemplate != "" ){
			
							$.merge( areaContent, sectionsTemplate )
							
						} else {
							set = that.sectionConfig( section )
							areaContent.push( set )
						
						}

					})

					areaSet = {
							name: areaName
						,	class: areaClass
						,	id: areaID
						,	content: areaContent
					}

					areaConfig.push( areaSet )

				})

				map[region] = areaConfig

			})
			
			return map
			
		}
		
		, sectionConfig: function( section ){
			
			var that = this
			,	set = {}

			set.object 	= section.data('object')
			set.clone 	= section.data('clone')
		
			set.sid 	= section.data('sid')
			set.span 	= that.getColumnSize( section )[ 4 ]
			set.offset 	= that.getOffsetSize( section )[ 3 ]
			set.newrow 	= (section.hasClass('force-start-row')) ? 'true' : 'false'
			set.content = []
			
			
			// Recursion
			section.find( '.pl-section.level2' ).each( function() {
			
				set.content.push( that.sectionConfig( $(this) ) )
				
			})
			
			return set
			
		}
		
		, getOffsetSize: function( column, defaultValue ) {
			
			var that = this
			,	max = 12
			,	sizes = that.getColumnSize( column )
			,	avail = max - sizes[4]
			,	data = []

			for( i = 0; i <= 12; i++){

					next = ( i == avail ) ? 0 : i+1

					prev = ( i == 0 ) ? avail : i-1	

					if(column.hasClass("offset"+i))
						data = new Array("offset"+i, "offset"+next, "offset"+prev, i)

			}

			if(data.length === 0 || defaultValue)
				return new Array("offset0", "offset0", "offset0", 0)
			else
				return data

		}
		

		, getColumnSize: function(column, defaultValue) {

			if (column.hasClass("span12") || defaultValue) //full-width
				return new Array("span12", "span2", "span10", "1/1", 12)

		    else if (column.hasClass("span10")) //five-sixth
		        return new Array("span10", "span12", "span9", "5/6", 10)

			else if (column.hasClass("span9")) //three-fourth
				return new Array("span9", "span10", "span8", "3/4", 9)

			else if (column.hasClass("span8")) //two-third
				return new Array("span8", "span9", "span6", "2/3", 8)

			else if (column.hasClass("span6")) //one-half
				return new Array("span6", "span8", "span4", "1/2", 6)

			else if (column.hasClass("span4")) // one-third
				return new Array("span4", "span6", "span3", "1/3", 4)

			else if (column.hasClass("span3")) // one-fourth
				return new Array("span3", "span4", "span2", "1/4", 3)

		    else if (column.hasClass("span2")) // one-sixth
		        return new Array("span2", "span3", "span12", "1/6", 2)

			else
				return false

		}
		
	}
	
}(window.jQuery);