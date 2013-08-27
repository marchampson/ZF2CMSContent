(function(){
	window.getTestJSON = function(index, options){
	var testObj = {};

		/*var width = 3;
		var zeroFilled = (new Array(width).join('0') + i).substr(-width);*/
		var id = Math.floor(Math.random() * 999) + 100;
		testObj.headers = ["pagename","featured", "status", "actions"];
	    testObj.data = [];

	    for(var i = 0; i < index; i++){
		    testObj.data.push({ 
		    	rowId: id,
		    	cells:[
			    	{
				    	"value": "Hello World " + i,
				    	"type": "string"
			    	},
			    	{
				    	"value": "true",
				    	"type": "button"
			    	},
			    	{
				    	"value": "Live",
				    	"type": "select",
				    	"options": ["Draft", "Live", "Private"]
			    	},
			    	{
			    		"type": "actions",
						actions: [
							{
								"url" : '/elements/content/item/edit/webpage/176',
								"type" : 'btn-warning edit',
								"text" : 'Edit'
							},
							{
								"url": '/elements/content/item/clone/webpage/176',
								"type": 'clone',
								"text" : 'Clone'
							},
							{
								"url" : '/elements/content/item/confirm-delete/webpage/176',
								"type" : 'btn-danger delete',
								"text" : 'Delete'
							}
						]
					}
				],
		    });
		}

		return testObj;
	};
}())