this["JST"] = this["JST"] || {};

this["JST"]["keymedia/alert"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  helpers = helpers || Handlebars.helpers;
  var buffer = "", stack1, foundHelper, helperMissing=helpers.helperMissing, escapeExpression=this.escapeExpression;


  buffer += "<p>";
  foundHelper = helpers.translate;
  stack1 = foundHelper ? foundHelper.call(depth0, "The image is too small for this crop", {hash:{}}) : helperMissing.call(depth0, "translate", "The image is too small for this crop", {hash:{}});
  buffer += escapeExpression(stack1) + "</p>\n";
  return buffer;});

this["JST"]["keymedia/browser"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  helpers = helpers || Handlebars.helpers;
  var buffer = "", stack1, foundHelper, helperMissing=helpers.helperMissing, escapeExpression=this.escapeExpression, functionType="function";


  buffer += "<form onsubmit=\"javascript: return false;\" class=\"form-search\">\n    <input type=\"text\" class=\"q input-long\" placeholder=\"";
  foundHelper = helpers.translate;
  stack1 = foundHelper ? foundHelper.call(depth0, "Search for media", {hash:{}}) : helperMissing.call(depth0, "translate", "Search for media", {hash:{}});
  buffer += escapeExpression(stack1) + "\">\n    <img style=\"margin: -1px 8px 0 -27px;\" class=\"icon-16 hide loader\" src=\"/extension/ezexceed/design/ezexceed/images/loader.gif\" />\n    <span class=\"upload-container\" id=\"keymedia-browser-local-file-container-";
  foundHelper = helpers.id;
  if (foundHelper) { stack1 = foundHelper.call(depth0, {hash:{}}); }
  else { stack1 = depth0.id; stack1 = typeof stack1 === functionType ? stack1() : stack1; }
  buffer += escapeExpression(stack1) + "\">\n        <button type=\"button\" class=\"btn upload\" id=\"keymedia-browser-local-file-";
  foundHelper = helpers.id;
  if (foundHelper) { stack1 = foundHelper.call(depth0, {hash:{}}); }
  else { stack1 = depth0.id; stack1 = typeof stack1 === functionType ? stack1() : stack1; }
  buffer += escapeExpression(stack1) + "\">\n            ";
  foundHelper = helpers.translate;
  stack1 = foundHelper ? foundHelper.call(depth0, "Upload new media", {hash:{}}) : helperMissing.call(depth0, "translate", "Upload new media", {hash:{}});
  buffer += escapeExpression(stack1) + "\n        </button>\n    </span>\n</form>\n<div class=\"keymedia-thumbs\"></div>\n";
  return buffer;});

this["JST"]["keymedia/item"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  helpers = helpers || Handlebars.helpers;
  var buffer = "", stack1, foundHelper, helperMissing=helpers.helperMissing, escapeExpression=this.escapeExpression, functionType="function", self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, foundHelper;
  buffer += "\n            <span class=\"share\">";
  foundHelper = helpers.translate;
  stack1 = foundHelper ? foundHelper.call(depth0, "Shared", {hash:{}}) : helperMissing.call(depth0, "translate", "Shared", {hash:{}});
  buffer += escapeExpression(stack1) + "</span>\n            ";
  return buffer;}

  buffer += "<div class=\"item\">\n    <a class=\"pick\" data-id=\"";
  foundHelper = helpers.id;
  if (foundHelper) { stack1 = foundHelper.call(depth0, {hash:{}}); }
  else { stack1 = depth0.id; stack1 = typeof stack1 === functionType ? stack1() : stack1; }
  buffer += escapeExpression(stack1) + "\">\n        <img src=\"";
  stack1 = depth0.thumb;
  stack1 = stack1 == null || stack1 === false ? stack1 : stack1.url;
  stack1 = typeof stack1 === functionType ? stack1() : stack1;
  buffer += escapeExpression(stack1) + "\" />\n        <p class=\"meta\">";
  foundHelper = helpers.filename;
  if (foundHelper) { stack1 = foundHelper.call(depth0, {hash:{}}); }
  else { stack1 = depth0.filename; stack1 = typeof stack1 === functionType ? stack1() : stack1; }
  buffer += escapeExpression(stack1) + "<br />\n            <span class=\"details\">";
  foundHelper = helpers.width;
  if (foundHelper) { stack1 = foundHelper.call(depth0, {hash:{}}); }
  else { stack1 = depth0.width; stack1 = typeof stack1 === functionType ? stack1() : stack1; }
  buffer += escapeExpression(stack1) + " x ";
  foundHelper = helpers.height;
  if (foundHelper) { stack1 = foundHelper.call(depth0, {hash:{}}); }
  else { stack1 = depth0.height; stack1 = typeof stack1 === functionType ? stack1() : stack1; }
  buffer += escapeExpression(stack1) + "</span>\n            ";
  stack1 = depth0.shared;
  stack1 = helpers['if'].call(depth0, stack1, {hash:{},inverse:self.noop,fn:self.program(1, program1, data)});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        </p>\n    </a>\n</div>\n";
  return buffer;});

this["JST"]["keymedia/scaledversion"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  helpers = helpers || Handlebars.helpers;
  var buffer = "", stack1, foundHelper, helperMissing=helpers.helperMissing, escapeExpression=this.escapeExpression, self=this, functionType="function";

function program1(depth0,data) {
  
  var buffer = "", stack1, foundHelper;
  buffer += "\n    <img class=\"white\" src=\"/extension/ezexceed/design/ezexceed/images/kp/24x24/white/Alert.png\"\n        alt=\"";
  foundHelper = helpers.translate;
  stack1 = foundHelper ? foundHelper.call(depth0, "Image is to small for this version", {hash:{}}) : helperMissing.call(depth0, "translate", "Image is to small for this version", {hash:{}});
  buffer += escapeExpression(stack1) + "\"\n        title=\"";
  foundHelper = helpers.translate;
  stack1 = foundHelper ? foundHelper.call(depth0, "Image is to small for this version", {hash:{}}) : helperMissing.call(depth0, "translate", "Image is to small for this version", {hash:{}});
  buffer += escapeExpression(stack1) + "\" />\n    <img class=\"black\" src=\"/extension/ezexceed/design/ezexceed/images/kp/24x24/Alert.png\"\n        alt=\"";
  foundHelper = helpers.translate;
  stack1 = foundHelper ? foundHelper.call(depth0, "Image is to small for this version", {hash:{}}) : helperMissing.call(depth0, "translate", "Image is to small for this version", {hash:{}});
  buffer += escapeExpression(stack1) + "\"\n        title=\"";
  foundHelper = helpers.translate;
  stack1 = foundHelper ? foundHelper.call(depth0, "Image is to small for this version", {hash:{}}) : helperMissing.call(depth0, "translate", "Image is to small for this version", {hash:{}});
  buffer += escapeExpression(stack1) + "\" />\n    ";
  return buffer;}

  buffer += "<a>\n    ";
  stack1 = depth0.toSmall;
  stack1 = helpers['if'].call(depth0, stack1, {hash:{},inverse:self.noop,fn:self.program(1, program1, data)});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    ";
  foundHelper = helpers.name;
  if (foundHelper) { stack1 = foundHelper.call(depth0, {hash:{}}); }
  else { stack1 = depth0.name; stack1 = typeof stack1 === functionType ? stack1() : stack1; }
  buffer += escapeExpression(stack1) + "<br />\n    <small>";
  foundHelper = helpers.width;
  if (foundHelper) { stack1 = foundHelper.call(depth0, {hash:{}}); }
  else { stack1 = depth0.width; stack1 = typeof stack1 === functionType ? stack1() : stack1; }
  buffer += escapeExpression(stack1) + "x";
  foundHelper = helpers.height;
  if (foundHelper) { stack1 = foundHelper.call(depth0, {hash:{}}); }
  else { stack1 = depth0.height; stack1 = typeof stack1 === functionType ? stack1() : stack1; }
  buffer += escapeExpression(stack1) + "</small>\n</a>\n<div class=\"overlay\"></div>\n";
  return buffer;});

this["JST"]["keymedia/scaler"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  helpers = helpers || Handlebars.helpers;
  var buffer = "", stack1, foundHelper, functionType="function", escapeExpression=this.escapeExpression;


  buffer += "<div class=\"customattributes\"></div>\n\n<section class=\"keymedia-crop\">\n    <ul class=\"nav nav-pills inverted\"></ul>\n</section>\n\n<div class=\"keymedia-crop-container\">\n    <div class=\"image-wrap\">\n        <img src=\"";
  foundHelper = helpers.media;
  if (foundHelper) { stack1 = foundHelper.call(depth0, {hash:{}}); }
  else { stack1 = depth0.media; stack1 = typeof stack1 === functionType ? stack1() : stack1; }
  buffer += escapeExpression(stack1) + "\" />\n    </div>\n</div>\n";
  return buffer;});

this["JST"]["keymedia/scalerattributes"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  helpers = helpers || Handlebars.helpers;
  var buffer = "", stack1, foundHelper, functionType="function", escapeExpression=this.escapeExpression, self=this, helperMissing=helpers.helperMissing;

function program1(depth0,data) {
  
  var buffer = "", stack1, foundHelper;
  buffer += " value=\"";
  foundHelper = helpers.alttext;
  if (foundHelper) { stack1 = foundHelper.call(depth0, {hash:{}}); }
  else { stack1 = depth0.alttext; stack1 = typeof stack1 === functionType ? stack1() : stack1; }
  buffer += escapeExpression(stack1) + "\"";
  return buffer;}

function program3(depth0,data) {
  
  var buffer = "", stack1, foundHelper;
  buffer += "\n    <label for=\"cssclass\">";
  foundHelper = helpers.translate;
  stack1 = foundHelper ? foundHelper.call(depth0, "Class", {hash:{}}) : helperMissing.call(depth0, "translate", "Class", {hash:{}});
  buffer += escapeExpression(stack1) + "</label>\n    <select name=\"cssclass\" id=\"cssclass\">\n        <option value=\"\"> - </option>\n        ";
  stack1 = depth0.classes;
  stack1 = helpers.each.call(depth0, stack1, {hash:{},inverse:self.noop,fn:self.program(4, program4, data)});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    </select>\n    ";
  return buffer;}
function program4(depth0,data) {
  
  var buffer = "", stack1, foundHelper;
  buffer += "\n            <option value=\"";
  foundHelper = helpers.value;
  if (foundHelper) { stack1 = foundHelper.call(depth0, {hash:{}}); }
  else { stack1 = depth0.value; stack1 = typeof stack1 === functionType ? stack1() : stack1; }
  buffer += escapeExpression(stack1) + "\"";
  stack1 = depth0.selected;
  stack1 = helpers['if'].call(depth0, stack1, {hash:{},inverse:self.noop,fn:self.program(5, program5, data)});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += ">";
  foundHelper = helpers.name;
  if (foundHelper) { stack1 = foundHelper.call(depth0, {hash:{}}); }
  else { stack1 = depth0.name; stack1 = typeof stack1 === functionType ? stack1() : stack1; }
  buffer += escapeExpression(stack1) + "</option>\n        ";
  return buffer;}
function program5(depth0,data) {
  
  
  return " selected";}

function program7(depth0,data) {
  
  var buffer = "", stack1, foundHelper;
  buffer += "\n    <label for=\"viewmode\">";
  foundHelper = helpers.translate;
  stack1 = foundHelper ? foundHelper.call(depth0, "View", {hash:{}}) : helperMissing.call(depth0, "translate", "View", {hash:{}});
  buffer += escapeExpression(stack1) + "</label>\n    <select name=\"viewmode\" id=\"viewmode\">\n        <option value=\"\"> - </option>\n        ";
  stack1 = depth0.viewmodes;
  stack1 = helpers.each.call(depth0, stack1, {hash:{},inverse:self.noop,fn:self.program(8, program8, data)});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    </select>\n    ";
  return buffer;}
function program8(depth0,data) {
  
  var buffer = "", stack1, foundHelper;
  buffer += "\n        <option value=\"";
  foundHelper = helpers.value;
  if (foundHelper) { stack1 = foundHelper.call(depth0, {hash:{}}); }
  else { stack1 = depth0.value; stack1 = typeof stack1 === functionType ? stack1() : stack1; }
  buffer += escapeExpression(stack1) + "\"\n        ";
  stack1 = depth0.selected;
  stack1 = helpers['if'].call(depth0, stack1, {hash:{},inverse:self.noop,fn:self.program(9, program9, data)});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += ">";
  foundHelper = helpers.name;
  if (foundHelper) { stack1 = foundHelper.call(depth0, {hash:{}}); }
  else { stack1 = depth0.name; stack1 = typeof stack1 === functionType ? stack1() : stack1; }
  buffer += escapeExpression(stack1) + "</option>\n        ";
  return buffer;}
function program9(depth0,data) {
  
  
  return " selected";}

  buffer += "<div class=\"well control-group\">\n    <input type=\"text\" name=\"alttext\"\n        placeholder=\"";
  foundHelper = helpers.translate;
  stack1 = foundHelper ? foundHelper.call(depth0, "Alternate text", {hash:{}}) : helperMissing.call(depth0, "translate", "Alternate text", {hash:{}});
  buffer += escapeExpression(stack1) + "\"\n        ";
  stack1 = depth0.alttext;
  stack1 = helpers['if'].call(depth0, stack1, {hash:{},inverse:self.noop,fn:self.program(1, program1, data)});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += ">\n\n    ";
  stack1 = depth0.classes;
  stack1 = helpers['if'].call(depth0, stack1, {hash:{},inverse:self.noop,fn:self.program(3, program3, data)});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n    ";
  stack1 = depth0.viewmodes;
  stack1 = helpers['if'].call(depth0, stack1, {hash:{},inverse:self.noop,fn:self.program(7, program7, data)});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n</div>\n";
  return buffer;});

this["JST"]["keymedia/show-more"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  helpers = helpers || Handlebars.helpers;
  var buffer = "", stack1, foundHelper, helperMissing=helpers.helperMissing, escapeExpression=this.escapeExpression;


  buffer += "<button class=\"btn btn-large btn-block load-more\" type=\"button\">\n    ";
  foundHelper = helpers.translate;
  stack1 = foundHelper ? foundHelper.call(depth0, "Show more", {hash:{}}) : helperMissing.call(depth0, "translate", "Show more", {hash:{}});
  buffer += escapeExpression(stack1) + "\n    <img class=\"icon-16 hide loader\" src=\"/extension/ezexceed/design/ezexceed/images/loader.gif\" />\n</button>\n";
  return buffer;});

this["JST"]["keymedia/tag"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  helpers = helpers || Handlebars.helpers;
  var buffer = "", stack1, foundHelper, functionType="function", escapeExpression=this.escapeExpression;


  buffer += "<span class=\"label\">";
  foundHelper = helpers.tag;
  if (foundHelper) { stack1 = foundHelper.call(depth0, {hash:{}}); }
  else { stack1 = depth0.tag; stack1 = typeof stack1 === functionType ? stack1() : stack1; }
  buffer += escapeExpression(stack1) + " <button class=\"close\" data-tag=\"";
  foundHelper = helpers.tag;
  if (foundHelper) { stack1 = foundHelper.call(depth0, {hash:{}}); }
  else { stack1 = depth0.tag; stack1 = typeof stack1 === functionType ? stack1() : stack1; }
  buffer += escapeExpression(stack1) + "\">×</button></span>\n";
  return buffer;});