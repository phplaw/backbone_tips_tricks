<?php
$workers = array();
$workers[] = array('name' => 'Misty May-Treanor', 'hometown' => 'Tokyo', 'age' => 29, 'active' => false);
$workers[] = array('name' => 'Kerri Walsh Jennings', 'hometown' => 'Paris', 'age' => 32, 'active' => true);
$workers[] = array('name' => 'Jennifer Kessy', 'hometown' => 'Rome', 'age' => 19, 'active' => false);
$workers[] = array('name' => 'April Ross', 'hometown' => 'Shanghai', 'age' => 17, 'active' => true);
//header('Content-Type: application/json');
?>
<!DOCTYPE html>
<html>
<head>
  <title>Backbone People list</title>
  <meta charset="utf-8">
  <script src="jquery-1.11.1.js"></script>
  <script src="underscore.js"></script>
  <script src="backbone.js"></script>
  <link rel="stylesheet" href="people.css">
</head>

<body>
  <script type="text/template" id="popup-template">
    <div class="popup_wrapper">
      <div class="pop_container_advanced">
        <div class="popup_content">
        <h2 class="dialog_title"><span><%= title %></span></h2>
        <div class="dialog_content"><%= content %></div>
        <div class="dialog_loading">Loading...</div>
        </div>
      </div>
    </div>
  </script>
  
  <script type="text/template" id="popup-person-add">
      <div class="dialog_body">
        <h3>Person Information</h3>
        <label>Name:<input name="name" value="" /></label>
        <label>Hometown: <input name="hometown" value="" /></label>
        <label>Age: <input name="age" value="" /></label>
      </div>
      <div class="dialog_buttons clearfix">
        <div class="rfloat mlm">
          <label class="uiButton uiButtonLarge">
            <input type="button" name="cancel" value="Cancel">
          </label>
          <label class="uiButton uiButtonLarge uiButtonConfirm">
            <input type="button" name="add" value="Add Person">
          </label>
        </div>
        <div class="dialog_buttons_msg"></div>
      </div>
  </script>
  
  <script type="text/template" id="popup-person-edit">
      <div class="dialog_body">
        <h3>Edit Person</h3>
        <label>Name:<input name="name" value="<%= name %>" /></label>
        <label>Hometown: <input name="hometown" value="<%= hometown %>" /></label>
        <label>Age: <input name="age" value="<%= age %>" /></label>
      </div>
      <div class="dialog_buttons clearfix">
        <div class="rfloat mlm">
          <label class="uiButton uiButtonLarge">
            <input type="button" name="cancel" value="Cancel">
          </label>
          <label class="uiButton uiButtonLarge uiButtonConfirm">
            <input type="button" name="ok" value="Update">
          </label>
        </div>
        <div class="dialog_buttons_msg"></div>
      </div>
  </script>
  
  <script type="text/template" id="popup-person-detail">
      <div class="dialog_body person_detail clearfix">
        <h3>Person Information</h3>
        <div class="avatar">Avatar</div>
        <label>Name: <%= name %></label>
        <label>Hometown: <%= hometown %></label>
        <label>Age: <%= age %>, Person is <%= active ? ' active' : ' not active' %></label>
      </div>
      
      <div class="dialog_buttons clearfix">
        <div class="rfloat mlm">
          <label class="uiButton uiButtonLarge">
            <input type="button" name="cancel" value="Close">
          </label>
        </div>
        <div class="dialog_buttons_msg"></div>
      </div>
  </script>
  
    <script type="text/template" id="popup-person-remove">
      <div class="dialog_body person_detail clearfix">
        Are you sure you wanna delete person <span class="fullname"><%= name %></span> from the list?
      </div>
      
      <div class="dialog_buttons clearfix">
        <div class="rfloat mlm">
        <label class="uiButton uiButtonLarge uiButtonConfirm">
            <input type="button" name="delete" value="Delete Person">
          </label>
          <label class="uiButton uiButtonLarge">
            <input type="button" name="cancel" value="Cancel">
          </label>
        </div>
        <div class="dialog_buttons_msg"></div>
      </div>
  </script>
  
  
  <script type="text/template" id="person-item">
    <td><a href="#" class="detail <%= active ? '' : 'inactive' %>"><%=name%></a></td>
    <td><%=hometown%></td>
    <td><%=age%></td>
    <td>
      <a href="#" class="edit">Edit</a>
      <a href="#" class="remove">Delete</a>
    </td>
    <td>        
      <input type="checkbox" class="active" name="active" <%= active ? '' : 'checked="checked"' %>/>
    </td>
  </script>
<script type="text/template" id="artists">
    <table class="reference">
      <thead>
        <tr>
          <th>Name</th>
          <th>Hometown</th>
          <th>Age</th>
          <th>Action</th>
          <th>Banned</th>
        </tr>
      </thead>
      <tbody class="rows"></tbody>
      <tfoot>
      <tr>
        <td colspan="3" align="right"></td>
        <td colspan="2"><a href="#" class="addPerson">Create New Person</a>
        </td>
      </tr>
      </tfoot>
    </table>
</script>

<script type="text/javascript">
  // Returns a random integer between min and max
// Using Math.round() will give you a non-uniform distribution!
function getRandomInt(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
  //Math.floor( Math.random() * 100 )
}
var Person = Backbone.Model.extend({
    urlRoot: 'person_model.php',
    defaults: {
    name: 'Sonny Nguyen',
    hometown: 'Phan Thiet',
    age: 30
  },
  
  validate: function(attrs, options) {
    if (attrs.name == '') {
      return "name can not be null";
    }
    
    if (attrs.hometown == '') {
      return "hometown can not be null";
    }
    
    if (attrs.age == '') {
      return "age can not be null";
    }
    if (attrs.age < 18) {
      return "age can not be under 18";
    }
  }
});

var People = Backbone.Collection.extend({
  model: Person,
  url: 'people.php'
});

//var Students = new People(<?php echo json_encode($workers) ?>);

jQuery(document).ready(function() {
  
  var Workers = <?php echo json_encode($workers); ?>;

//Everything you need to know about underscore template is here. Only 3 things to keep in mind:
//
//<%  %> - to execute some code
//<%= %> - to print some value in template
//<%- %> - to print some values with HTML escaped
//That's all about it.
var personPopup = Backbone.View.extend({
      popContent: '',
      tagName: "div",
      className: "overlay",
      template: $('#popup-template').html(),
      initialize: function (options) { 
        this.popContent = options.content || $('#popup-person-edit').html();
        this.popTitle = options.title || 'Information';
        //this.model =  options.model || {};
        //this.popTitle = options.title || $('#popup-person-edit').html();
        //alert(options.content); 
      },
      //render: function(options) { 
      //this.delegateEvents(_.extend(this.events, {'click select': 'changeSelect'}));
      //this.delegateEvents( _.extend(this.events, options.events) );
      render: function() {
        var pop_content = _.template(this.popContent, {
          name: this.model.get('name'), 
          age: this.model.get('age'), 
          hometown: this.model.get('hometown'),
          active: this.model.get('active')
        });
        
        var popupData = {title: this.popTitle, content: pop_content};
        var html = _.template(this.template, popupData);
        this.$el.html(html);
        $('body').append(this.el);
      },

      events: {
        "click input[name=ok]":          "update",
        "click input[name=delete]":          "modelDelete",
        "click input[name=cancel]":      "close"
      },

      modelDelete: function(e) {
        this.model.destroy();
        this.remove();
      },
      
      close: function(e) {
        this.remove();
      },

      update: function(e) {
        var update = {
        name: this.$('input[name=name]').val(),
        age: this.$('input[name=age]').val(),
        hometown: this.$('input[name=hometown]').val()
        };
        this.model.set(update);
        this.remove();
      }
    });
    
var personView  = Backbone.View.extend({
  tagName: 'tr',
  template: $('#person-item').html(),
  initialize: function(){
    //se backbone events for futher information
      this.model.bind('change', this.render, this);
      this.model.bind('destroy', this.remove, this);
    },
  render: function() {
    this.$el.html( _.template( this.template, this.model.toJSON() ) );
    return this.el;
  },
  events: {
     'click a.detail': 'personDetail', 
     'click a.edit': 'editPerson', 
     'click a.remove': 'removePerson',
     'click input.active': 'setActive'
  },
  setActive: function() {
     //this.model.save({active: !this.model.get('active')});
     this.model.set({active: !this.model.get('active')});
  },
  removePerson: function(e) {
    //confirm popup
    var myPopup = new personPopup({
      'title': 'Delete Person', 
      content: $('#popup-person-remove').html(), 
      model: this.model
    });
     myPopup.render();
     e.preventDefault(); 
  },
  editPerson: function(e) {
     e.preventDefault();
     console.log('Edit A Person');
     console.log(this.model.toJSON());
     var myPopup = new personPopup({model: this.model});
     myPopup.render();
  },
  personDetail: function(e) {
     e.preventDefault();
     //alert($('#popup-person-detail').html());
     var myPopup = new personPopup({content: $('#popup-person-detail').html(), model: this.model});
     myPopup.render();
  }
});

  var listPeople = Backbone.View.extend({
    tagName: 'div', 
    className: 'listPeople', 
    id: 'list_people',
    initialize: function(){
        //this.collection = new BookList();
        //this.collection.bind('reset', this.render, this);
        //this.collection.bind('add', this.render, this);
        //this.collection.fetch();

    },
    render: function() {
      this.$el.html( $('#artists').html() );
      
      $('body').append( this.el );
      //insert data to view
      _.each(Workers, function(Worker) {
        
        var newPerson = new personView({model: new Person(Worker)});
        this.$('tbody').append(newPerson.render());
        
      });
        
      //this.collection.fetch({ success: function () { self.render(); } });
      //Students.fetch({ success: function() {}});
      /*Students.each(function(student) {
        console.log(student.toJSON());
        var newPerson = new personView({ model: student });
        this.$('tbody').append(newPerson.render());
      });*/
      
    },
    
    events: {
      'click a.edit': 'editPerson', 
      'click a.remove': 'removePerson',
      'click a.addPerson': 'addPerson'
    },
    
    addPerson: function(e) {
      e.preventDefault();
      var emptyPerson = new Person({name: '', age: '', hometown: '', active: true});
      var self = this;
  
      var myPopup = new personPopup({
        title: 'Create New Person',
        content: $('#popup-person-add').html(), 
        model: emptyPerson,
        events: {
        "click input[name=cancel]":      "close",
        "click input[name=add]": function() {
            var update = {
              name: myPopup.$('input[name=name]').val(),
              age: myPopup.$('input[name=age]').val(),
              hometown: myPopup.$('input[name=hometown]').val(),
              active: true
            };
            
            emptyPerson.set(update);
            //validate person before save It
            if (!emptyPerson.isValid()) {
              this.$('div.dialog_body').append('<div class="error">'  + emptyPerson.get("name") + " " + emptyPerson.validationError + '<div>');
              window.setTimeout(function() {
                myPopup.$('div.error').fadeOut();
              }, 2000);
              //alert(emptyPerson.get("name") + " " + emptyPerson.validationError);
              return;
            }
            
            var newPerson = new personView({model: emptyPerson });
            self.$('tbody').append(newPerson.render());
            this.remove();
          }
        }
      });
      
      myPopup.render();
     /*
      console.log('Add New Person');
      var newPerson = new personView({model: new Person({ age: getRandomInt(18, 40) })});
      this.$('tbody').append(newPerson.render());*/
    }
  });
  
  var students = new listPeople();
  students.render();
//model with default value
//new Book({
//  title: "One Thousand and One Nights",
//  author: "Scheherazade"
//});

//extending view events
Backbone.View.prototype.addEvents = function(events) {
  this.delegateEvents( _.extend(_.clone(this.events), events) );
};
// inside the view
//this.delegateEvents(_.extend(this.events, {'click select': 'changeSelect'}));

});

</script>
</body>
</html>
