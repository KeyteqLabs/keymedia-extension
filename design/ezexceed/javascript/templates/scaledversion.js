define(['handlebars'], function(Handlebars) {

return Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [2,'>= 1.0.0-rc.3'];
helpers = helpers || Handlebars.helpers; data = data || {};
  var buffer = "", stack1, helperMissing=helpers.helperMissing, escapeExpression=this.escapeExpression, self=this, functionType="function";

function program1(depth0,data) {
  
  var buffer = "", stack1, options;
  buffer += "\n    <img class=\"white\" src=\"/extension/ezexceed/design/ezexceed/images/kp/24x24/white/Alert.png\"\n        alt=\"";
  options = {hash:{},data:data};
  buffer += escapeExpression(((stack1 = helpers.translate),stack1 ? stack1.call(depth0, "Image is to small for this version", options) : helperMissing.call(depth0, "translate", "Image is to small for this version", options)))
    + "\"\n        title=\"";
  options = {hash:{},data:data};
  buffer += escapeExpression(((stack1 = helpers.translate),stack1 ? stack1.call(depth0, "Image is to small for this version", options) : helperMissing.call(depth0, "translate", "Image is to small for this version", options)))
    + "\" />\n    <img class=\"black\" src=\"/extension/ezexceed/design/ezexceed/images/kp/24x24/Alert.png\"\n        alt=\"";
  options = {hash:{},data:data};
  buffer += escapeExpression(((stack1 = helpers.translate),stack1 ? stack1.call(depth0, "Image is to small for this version", options) : helperMissing.call(depth0, "translate", "Image is to small for this version", options)))
    + "\"\n        title=\"";
  options = {hash:{},data:data};
  buffer += escapeExpression(((stack1 = helpers.translate),stack1 ? stack1.call(depth0, "Image is to small for this version", options) : helperMissing.call(depth0, "translate", "Image is to small for this version", options)))
    + "\" />\n    ";
  return buffer;
  }

  buffer += "<a>\n    ";
  stack1 = helpers['if'].call(depth0, depth0.toSmall, {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    ";
  if (stack1 = helpers.name) { stack1 = stack1.call(depth0, {hash:{},data:data}); }
  else { stack1 = depth0.name; stack1 = typeof stack1 === functionType ? stack1.apply(depth0) : stack1; }
  buffer += escapeExpression(stack1)
    + "<br />\n    <small>";
  if (stack1 = helpers.width) { stack1 = stack1.call(depth0, {hash:{},data:data}); }
  else { stack1 = depth0.width; stack1 = typeof stack1 === functionType ? stack1.apply(depth0) : stack1; }
  buffer += escapeExpression(stack1)
    + "x";
  if (stack1 = helpers.height) { stack1 = stack1.call(depth0, {hash:{},data:data}); }
  else { stack1 = depth0.height; stack1 = typeof stack1 === functionType ? stack1.apply(depth0) : stack1; }
  buffer += escapeExpression(stack1)
    + "</small>\n</a>\n<div class=\"overlay\"></div>\n";
  return buffer;
  })

});