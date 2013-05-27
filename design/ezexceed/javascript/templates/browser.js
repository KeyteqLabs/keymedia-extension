define(['handlebars'], function(Handlebars) {

return Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [2,'>= 1.0.0-rc.3'];
helpers = helpers || Handlebars.helpers; data = data || {};
  var buffer = "", stack1, stack2, options, helperMissing=helpers.helperMissing, escapeExpression=this.escapeExpression, functionType="function";


  buffer += "<form onsubmit=\"javascript: return false;\" class=\"form-search\">\n    <input type=\"text\" class=\"q input-long\" placeholder=\"";
  options = {hash:{},data:data};
  buffer += escapeExpression(((stack1 = helpers.translate),stack1 ? stack1.call(depth0, "Search for media", options) : helperMissing.call(depth0, "translate", "Search for media", options)))
    + "\">\n    <img style=\"margin: -1px 8px 0 -27px;\" class=\"icon-16 hide loader\" src=\"/extension/ezexceed/design/ezexceed/images/loader.gif\" />\n    <span class=\"upload-container\" id=\"keymedia-browser-local-file-container-";
  if (stack2 = helpers.id) { stack2 = stack2.call(depth0, {hash:{},data:data}); }
  else { stack2 = depth0.id; stack2 = typeof stack2 === functionType ? stack2.apply(depth0) : stack2; }
  buffer += escapeExpression(stack2)
    + "\">\n        <button type=\"button\" class=\"btn upload\" id=\"keymedia-browser-local-file-";
  if (stack2 = helpers.id) { stack2 = stack2.call(depth0, {hash:{},data:data}); }
  else { stack2 = depth0.id; stack2 = typeof stack2 === functionType ? stack2.apply(depth0) : stack2; }
  buffer += escapeExpression(stack2)
    + "\">\n            ";
  options = {hash:{},data:data};
  buffer += escapeExpression(((stack1 = helpers.translate),stack1 ? stack1.call(depth0, "Upload new media", options) : helperMissing.call(depth0, "translate", "Upload new media", options)))
    + "\n        </button>\n    </span>\n</form>\n<div class=\"keymedia-thumbs\"></div>\n<button class=\"btn btn-large btn-block load-more\" type=\"button\">\n    ";
  options = {hash:{},data:data};
  buffer += escapeExpression(((stack1 = helpers.translate),stack1 ? stack1.call(depth0, "Load more", options) : helperMissing.call(depth0, "translate", "Load more", options)))
    + "\n    <img class=\"icon-16 hide loader\" src=\"/extension/ezexceed/design/ezexceed/images/loader.gif\" />\n</button>\n";
  return buffer;
  })

});