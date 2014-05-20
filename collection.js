var ships = new Backbone.Collection;

ships.on("add", function(ship) {
  alert("Ahoy " + ship.get("name") + "!");
});

ships.add([
  {name: "Flying Dutchman"},
  {name: "Black Pearl"}
]);


var myView = Backbone.View.extend({
  initialize: function() {
    this.collection = new ProjectsCollection();
    this.collection.bind("reset", _.bind(this.render, this));
    this.collection.fetch({});
  }
});