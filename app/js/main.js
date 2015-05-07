var App = angular.module('App', []);

App.controller('ListCtrl', function($scope, $http) {
	$http.get('http://mikekorostelev.com/~bits/self/db/users').then(function(res) {
		//console.log("asdfasfd")
		$scope.users = res.data;

	});

	console.log($scope.selected);

	$scope.update = function(selected) {
		console.log(selected);
		displayUserFromDb(selected.user,populateTable);
	}
	
	
	
});

function displayUserFromDb(name, callback) {
	$.ajax({
		type : "GET",
		url : "http://mikekorostelev.com/~bits/self/db/user/" + name,
		dataType : 'json',
		success : function(data) {
			console.log("got user");
			// console.log(data._id);
			
			//poplulate table
			callback(data);
			$('.user-id').text(data._id.$id);
			// $('.user-name').text(data.user);
			// $('.user-email').text(data.email);
			 $('.user-password').text(data.password);
			 $('.user-api_key').text(data.api_key);
			// $('.user-p').text(data.r);
			// $('.user-r').text(data.r);
			// $('.user-e').text(data.e);
			// $('.user-ti').text(data.ti);
			// $('.user-tp').text(data.tp);
			// $('.user-contexts').text(data.contexts);

		},
		error : function(jqXHR, textStatus, errorThrown) {
			console.log(errorThrown);
		}
	});
}


// call back for displayUserFromDb
function populateTable (userAttributes) {
		console.log("table");
		json = JSON.stringify(userAttributes);
		console.log(json);
		json = JSON.parse(json);

		var arr = Object.keys(json).map(function(k) {
			return json[k]
		});

		console.log(arr);
		arr[0] = userAttributes._id.$id;
		console.log(userAttributes.user);
		$('#example').dataTable({
			destroy: true,
			"data" : [arr],
			"columnDefs": [
            {
                "targets": [ 0,3,4 ],
                "visible": false
            }
        ]
		});
	}


$(document).ready(function() {
	
	//TODO register user form
$('#example').dataTable();
	displayUserFromDb('mike', populateTable);

});

