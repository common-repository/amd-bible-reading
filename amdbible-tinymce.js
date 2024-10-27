 ( function() {
    tinymce.PluginManager.add( 'amdbible_button', function( editor, url ) {

        // Add a button that opens a window
        editor.addButton( 'amd_passage_key', {

            text: ' Passage',
            image: url + '/amd.jpg',
            //icon: false,
            onclick: function() {
                // Open window
                editor.windowManager.open( {
                    title: 'AMD Bible Passage',
					body: [{
                        type: 'textbox',
                        name: 'title',
                        label: 'Bible Reference'
                    },{
                        type: 'checkbox',
						checked: true,
                        name: 'inline',
                        label: 'Inline'
                    },{
                        type: 'textbox',
						value: '0',
                        name: 'limit',
                        label: 'Limit (0 = unlimited)'
                    },{
                        type: 'listbox',
                        name: 'limit_type',
                        label: 'Limit by',
						values: [
							{text: 'Select', value: ''},
							{text: 'Words', value: 'words'},
							{text: 'Verses', value: 'verses'},
						],
						value: ''
                    },{
                        type: 'checkbox',
						checked: false,
                        name: 'show_book',
                        label: 'Show Book Title'
                    },{
                        type: 'checkbox',
						checked: false,
                        name: 'show_chapter',
                        label: 'Show Chapter Number'
                    },{
                        type: 'checkbox',
						checked: false,
                        name: 'show_verse',
                        label: 'Show Verse Number'
                    },{
                        type: 'checkbox',
						checked: false,
                        name: 'reference_before',
                        label: 'Show Reference Before'
                    },{
                        type: 'checkbox',
						checked: true,
                        name: 'reference_after',
                        label: 'Show Reference After'
                    }],
					// more documentation can be found at:
					// https://www.tinymce.com/docs/api/tinymce.ui/
                    onsubmit: function( e ) {
                        // Insert content when the window form is submitted
                        var attributes = '';
						if(!e.data.inline){
							attributes += ' inline=false';
						}
						if(!isNaN(e.data.limit) && e.data.limit>0 && (e.data.limit_type=='verses' || e.data.limit_type=='words')){
							attributes += ' limit='+e.data.limit+" limit_type='"+e.data.limit_type+"'";
						}
						if(e.data.show_book){
							attributes += ' show_book=true';
						} else {
							attributes += ' show_book=false';
						}
						if(e.data.show_chapter){
							attributes += ' show_chapter=true';
						} else {
							attributes += ' show_chapter=false';
						}
						if(e.data.show_verse){
							attributes += ' show_verse=true';
						} else {
							attributes += ' show_verse=false';
						}
						if(e.data.reference_before){
							attributes += ' reference_before=true';
						} else {
							attributes += ' reference_before=false';
						}
						if(e.data.reference_after){
							attributes += ' reference_after=true';
						} else {
							attributes += ' reference_after=false';
						}
						editor.insertContent( '[amd_bible' + attributes + ']' + e.data.title +'[/amd_bible]');
                    }

                } );
            }

        } );
		
		// Add a button that opens a window
        editor.addButton( 'amd_daily_bible_key', {

            text: ' Daily Reading',
            image: url + '/amd.jpg',
            //icon: false,
            onclick: function() {
                // Open window
                editor.windowManager.open( {
                    title: 'AMD Bible Daily Reading',
                    body: [{
                        type: 'textbox',
                        name: 'plan',
                        label: 'Reading Plan (number only. 0 for default.)'
                    },{
                        type: 'checkbox',
						checked: false,
                        name: 'inline',
                        label: 'Inline'
                    },{
                        type: 'textbox',
						value: '0',
                        name: 'limit',
                        label: 'Limit (0 = unlimited)'
                    },{
                        type: 'listbox',
                        name: 'limit_type',
                        label: 'Limit by',
						values: [
							{text: 'Select', value: ''},
							{text: 'Words', value: 'words'},
							{text: 'Verses', value: 'verses'},
						],
						value: ''
                    },{
                        type: 'checkbox',
						checked: true,
                        name: 'show_book',
                        label: 'Show Book Title'
                    },{
                        type: 'checkbox',
						checked: true,
                        name: 'show_chapter',
                        label: 'Show Chapter Number'
                    },{
                        type: 'checkbox',
						checked: true,
                        name: 'show_verse',
                        label: 'Show Verse Number'
                    },{
                        type: 'checkbox',
						checked: true,
                        name: 'reference_before',
                        label: 'Show Reference Before'
                    },{
                        type: 'checkbox',
						checked: false,
                        name: 'reference_after',
                        label: 'Show Reference After'
                    },{
                        type: 'checkbox',
						checked: true,
                        name: 'form_before',
                        label: 'Show Form Before'
                    },{
                        type: 'checkbox',
						checked: true,
                        name: 'form_after',
                        label: 'Show Form After'
                    },{
                        type: 'textbox',
                        name: 'day',
                        label: 'Day of the Year (empty for current day)'
                    },{
                        type: 'textbox',
                        name: 'date',
                        label: 'A Date String (empty for current day)'
                    },{
                        type: 'textbox',
                        name: 'date_format',
                        label: 'Valid PHP Date Format',
						value: 'D., M. j, Y'
                    },{
                        type: 'textbox',
                        name: 'no_reading_text',
                        label: 'Text When There is No Reading Scheduled',
						value: 'There is no reading scheduled for this day. Use this day to catch up or read ahead.',
						multiline: true
                    },],
					// more documentation can be found at:
					// https://www.tinymce.com/docs/api/tinymce.ui/
                    onsubmit: function( e ) {
                        // Insert content when the window form is submitted
                        var attributes = '';
						if(e.data.inline){
							attributes += ' inline=true';
						}
						if(!isNaN(e.data.limit) && e.data.limit>0 && (e.data.limit_type=='verses' || e.data.limit_type=='words')){
							attributes += ' limit='+e.data.limit+" limit_type='"+e.data.limit_type+"'";
						}
						if(!isNaN(e.data.plan) && e.data.plan>0){
							attributes += " plan='"+e.data.plan+"'";
						}
						if(e.data.show_book){
							attributes += ' show_book=true';
						} else {
							attributes += ' show_book=false';
						}
						if(e.data.show_chapter){
							attributes += ' show_chapter=true';
						} else {
							attributes += ' show_chapter=false';
						}
						if(e.data.show_verse){
							attributes += ' show_verse=true';
						} else {
							attributes += ' show_verse=false';
						}
						if(e.data.reference_before){
							attributes += ' reference_before=true';
						} else {
							attributes += ' reference_before=false';
						}
						if(e.data.reference_after){
							attributes += ' reference_after=true';
						} else {
							attributes += ' reference_after=false';
						}
						if(e.data.form_before){
							attributes += ' form_before=true';
						} else {
							attributes += ' form_before=false';
						}
						if(e.data.form_after){
							attributes += ' form_after=true';
						} else {
							attributes += ' form_after=false';
						}
						if(e.data.day && !isNaN(e.data.day)){
							attributes += " day='"+e.data.day+"'";
						} else if(e.data.date) {
							attributes += " date='"+e.data.date+"'";
						}
						if(e.data.date_format){
							attributes += " date_format='"+e.data.date_format+"'";
						}
						if(e.data.no_reading_text){
							attributes += " no_reading_text='"+e.data.no_reading_text+"'";
						}
						editor.insertContent( '[amd_bible_daily' + attributes + ']');
                    }

                } );
            }

        } );
		
		// Add a button that opens a window
        editor.addButton( 'amd_devo_key', {

            text: ' Devo',
            image: url + '/amd.jpg',
            //icon: false,
            onclick: function() {
				
                // Add Shortcode
				editor.insertContent( '[amd_bible_devo]');
				
			}

        } );
		
		
        // Add a button that opens a window
        editor.addButton( 'amd_rand_verse_key', {

            text: ' Random',
            image: url + '/amd.jpg',
            //icon: false,
            onclick: function() {
                // Open window
                editor.windowManager.open( {
                    title: 'AMD Random Verse',
                    body: [{
                        type: 'listbox',
                        name: 'rand_type',
                        label: 'Random Verse List',
						values: [
							{text: 'Options Below', value: 'bible'},
							{text: 'Most Read Verses', value: 'most_read'},
							{text: 'Faith Essential Verses', value: 'essential'},
						],
						value: ''
                    },{
                        type: 'checkbox',
						checked: true,
                        name: 'ot',
                        label: 'Old Testament'
                    },{
                        type: 'checkbox',
						checked: true,
                        name: 'nt',
                        label: 'New Testament'
                    },{
                        type: 'textbox',
						value: '',
                        name: 'book',
                        label: 'Book of the Bible'
                    },{
                        type: 'textbox',
						value: '',
                        name: 'chapter',
                        label: 'Chapter of the above Book'
                    }],
					// more documentation can be found at:
					// https://www.tinymce.com/docs/api/tinymce.ui/
                    onsubmit: function( e ) {
                        // Insert content when the window form is submitted
                        var attributes = '';
						if(e.data.rand_type=='bible'){
							if((e.data.ot && !e.data.nt) || (!e.data.ot && e.data.nt)){
								if(e.data.ot) {
									attributes += ' ot=true';
								} else if(e.data.nt){
									attributes += ' nt=true';
								}
							} else {
								if(e.data.book){
									attributes += " book='"+e.data.book+"'";
									if(e.data.chapter){
										attributes += " book='"+e.data.chapter+"'";
									}
								}
							}
						} else if(e.data.rand_type=='most_read'){
							attributes += ' most_read=true';
						} else if(e.data.rand_type=='essential'){
							attributes += ' essential=true';
						}
						editor.insertContent( '[amd_bible_rand' + attributes + ']');
                    }

                } );
            }

        } );

    } );

} )();

