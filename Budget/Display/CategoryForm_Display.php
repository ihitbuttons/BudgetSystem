				<div id='AddCategoriesForm' ng-hide="loggedIN">
				
				<p>Category Name:</p>
				<input type='text' id='addCategoryName' name='addCategoryName' ng-model='categoryDetailsReturned.0.categoryName'></input><br/><br/>
				
				<div id='buttonOne' ng-show="categoryAdd">
				<button ng-click="apicall('budgetAPI', 'addCategory', 'categoryName', loadById('addCategoryName'))">Add Category</button>
				<br/><br/>
				</div>
				
				<div id='buttonTwo' ng-hide="categoryAdd">
				<button ng-click="apicall('budgetAPI', 'modifyCategory', 'categoryPK', loadById('categoryDetails'), 'categoryName', loadById('addCategoryName'))">Modify Category</button>
				<br/><br/>
				</div>
				
				</div>
