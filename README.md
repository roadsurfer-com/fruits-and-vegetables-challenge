# 🍎🥕 Fruits and Vegetables

## 🎯 Goal
We want to build a service which will take a `request.json` and:
* [x] Process the file and create two separate collections for `Fruits` and `Vegetables`
* [x] Each collection has methods like `add()`, `remove()`, `list()`;
* [x] Units have to be stored as grams;
* [x] Store the collections in a storage engine of your choice. (e.g. Database, In-memory)
* [x] Provide an API endpoint to query the collections. As a bonus, this endpoint can accept filters to be applied to the returning collection.
* [x] Provide another API endpoint to add new items to the collections (i.e., your storage engine).
* [x] As a bonus you might:
  * [x] consider giving an option to decide which units are returned (kilograms/grams);
  * [x] how to implement `search()` method collections;
  * [x] use latest version of Symfony's to embed your logic
  
  * [x] Provide API documentation
    * use https://editor.swagger.io/ to read generated openapi.json file via command `console nelmio:apidoc:dump > openapi.json`

### ✔️ How can I check if my code is working?
You have two ways of moving on:
* [x] You call the Service from PHPUnit test like it's done in dummy test (just run `bin/phpunit` from the console)

or

* [ ] You create a Controller which will be calling the service with a json payload

## 💡 Hints before you start working on it
* [x] Keep KISS, DRY, YAGNI, SOLID principles in mind
* [x] Timebox your work - we expect that you would spend between 3 and 4 hours.
* [x] Your code should be tested

## When you are finished
* Please upload your code to a public git repository (i.e. GitHub, Gitlab)
