var tag = {
  list : [], // Existing list of tags
  add : function (evt) {
  // tag.add() : press comma or enter to add tag

    if (evt.key=="," || evt.key=="Enter") {
      // Input check
      var tagged = evt.key=="," ? this.value.slice(0, -1) : this.value,
          error = "";

      // Freaking joker empty input
      if (tagged=="") {
        error = "Please enter a valid tag";
      }

      // Check if already in tags list
      if (error=="") {
        if (tag.list.indexOf(tagged) != -1) {
          error = tagged + " is already defined";
        }
      }

      // OK - Create new tag
      if (error=="") {
        var newTag = document.createElement("div");
        newTag.classList.add("tag");
        newTag.innerHTML = tagged;
        newTag.addEventListener("click", tag.remove);
        document.getElementById("tag_list").appendChild(newTag);
        tag.list.push(tagged);
        this.value = "";
      }

      // Not OK - Show error message
      else {
        this.value = tagged;
        alert(error);
      }
    }
  },

  remove : function () {
  // tag.remove() : remove tag

    // Remove tag from list array first
    // var pos = tag.list.indexOf(this.innerHTML);
    tag.list.splice(tag.list.indexOf(this.innerHTML), 1);

    // Remove HTML tag
    document.getElementById("tag_list").removeChild(this);
  },

  save : function () {
  // tag.save() : save the tags

    // DATA
    var data = new FormData();
    data.append('req', 'save');
    data.append('post_id', document.getElementById('post_id').value);
    data.append('tags', JSON.stringify(tag.list));

    // AJAX
    var xhr = new XMLHttpRequest();
    xhr.open('POST', "2c-ajax-tag.php", true);
    xhr.onload = function(){
      var res = JSON.parse(this.response);
      // OK
      if (res.status==1) {
        alert("Save OK");
      } else {
        alert(res.message);
      }
    };
    xhr.send(data);
    return false;
  }
};

// INIT ON WINDOW LOAD
window.addEventListener("load", function() {
  // Get list of existing tags
  var all = document.querySelectorAll("#tag_list div.tag");
  if (all.length>0) {
    for (var t of all) {
      tag.list.push(t.innerHTML);
      // Attach remove listener to tags
      t.addEventListener("click", tag.remove);
    }
  }

  // Attach comma listener to input field
  document.getElementById("tag_in").addEventListener("keyup", tag.add);

  // Enable controls
  document.getElementById("tag_in").disabled = false;
  document.getElementById("tag_save").disabled = false;
});