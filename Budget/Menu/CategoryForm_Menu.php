				<center>
				<button ng-click="apicall('budgetAPI', 'listCategories')">Refresh Categories</button><br/>
				<br/><br/>
				<select id='categoryDetails' ng-model="selectedItem">
					<option ng-repeat="listCategoriesItem in listCategoriesReturned" value="{{ listCategoriesItem.categoryPK }}">
					{{ listCategoriesItem.categoryName }}
					</option>
				</select>
				<br/><br/>
				<button ng-click="apicall('budgetAPI', 'categoryDetails', 'categoryPK', loadById('categoryDetails')); categoryAdd='false';">Category Details</button>
				<br/><br/>
				</center>