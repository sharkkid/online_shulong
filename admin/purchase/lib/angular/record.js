// JavaScript Document

app.controller("recordCtrl", ['$scope', 'records', function($scope, records){	
   // load from a web services somewhere
   
    // HTTP Service Calls In
   records.getRecords(function(data) {
      $scope.records = data;
   });
   
   // Simple sort method and filter search
   $scope.sortType = "Name"; // Set the default sort type
   $scope.sortReverse  = false;  // set the default sort order
   $scope.searchRecord   = '';     // set the default search/filter term
   // set up some default editing variable
   $scope.hide = true;  // Hide the editing form
   $scope.edit = true;  // Set to true when user clicks on create user. 
   $scope.incomplete = false;  // Make sure the field is not empty
   $scope.isFormSubmit = false; // determine if the form has been submitted
   
   // Reset Form function 
   // --------------------------------------------------------------------
   $scope.Reset = function () {
	    // assign blank value to all variable inside scope
		$scope.Name = '';
		$scope.JobTitle = '';
		$scope.Location = '';
		$scope.Email = '';
		$scope.PhoneNumber = '';
   };
   
   // Edit User function 
   // --------------------------------------------------------------------
   $scope.editUser = function(id){
		// show the edit form
		$scope.hide = false;
		// Create new user event, if the new user click event was clicked
		if (id == 'new'){
			$scope.edit = true;	 // For the h3 heading change
			$scope.Reset();   
			
		// Edit user event
		}else{
			$scope.edit = false; // For the h3 heading change
			// retrive the user information and pass into the input field
			$scope.myId = $scope.records[id-1].id;
			$scope.Name = $scope.records[id-1].Name;
			$scope.JobTitle = $scope.records[id-1].JobTitle;
			$scope.Location = $scope.records[id-1].Location;
			$scope.Email = $scope.records[id-1].Email;
			$scope.PhoneNumber = $scope.records[id-1].PhoneNumber;	
		}
		
   };
   
   // Save Data to the scope list
   $scope.saveRecord = function(){
		
		if ($scope.edit == true){
			// If is new user
			$scope.records.push({
				id:$scope.records.length + 1,
				Name: $scope.Name,
				JobTitle: $scope.JobTitle,
				Location: $scope.Location,
				Email: $scope.Email,
				PhoneNumber: $scope.PhoneNumber
			});
		}else{
			// Save the edit information
			//console.log($scope.myId);
			$scope.updateRecord($scope.myId);
			//console.log("saved");
		};
		
		
		// reset all form value
		$scope.Reset();   
		
		// Close window
		$scope.closeWindow();
   };
   
   // Update Record
   $scope.updateRecord = function(id){
	   	 // iterate over the array to match the correct id, then modify the array value
		 for(i=0;i<$scope.records.length;++i) {
		 	var records = $scope.records[i];
			 if ( records.id === id) {
				  records.Name = $scope.Name,
				  records.JobTitle = $scope.JobTitle,
				  records.Location = $scope.Location,
				  records.Email = $scope.Email,
				  records.PhoneNumber = $scope.PhoneNumber
			 }
		 };
   };
   
   
   // Delete User Function
   // --------------------------------------------------------------------
   $scope.deleteUser = function(id){
		// Use default confirmation dialog box
		if (confirm("Are you sure you want to delete?")) {
        // todo code for deletion
			$scope.records.splice(id-1, 1); 
    	}
          
   };
   
   
   // Close the user form window
   // --------------------------------------------------------------------
   $scope.closeWindow = function(){
		$scope.hide = true;
   };
   
   
   // Submit the form after all validation has occurred
   // --------------------------------------------------------------------  
   $scope.submitForm = function(isValid) {
		
		// check to make sure the form is completely valid
		if (isValid) {
		  //console.log('Validation successful');
		  // Save the record once the form is validated
		  $scope.saveRecord();
		}else{
			// set form submit to true to show error message
			$scope.isFormSubmit = true;		
		}

   };
   
   
   
}]);


