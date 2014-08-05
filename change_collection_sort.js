var SomeCollection = Backbone.Collection.extend({
    comparator: function (property) {
        return selectedStrategy.apply(myModel.get(property));
    },
    strategies: {
        firstName: function (person) { return person.get("firstName"); }, 
        lastName: function (person) { return person.get("lastName"); },
    },
    changeSort: function (sortProperty) {
        this.comparator = this.strategies[sortProperty];
    },
    initialize: function () {
        this.changeSort("lastName");
        console.log(this.comparator);
        this.changeSort("firstName");
        console.log(this.comparator);        
    }                                                                                        
});

var myCollection = new SomeCollection;
