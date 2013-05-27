define(['handlebars'], function(Handlebars) {

return Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [2,'>= 1.0.0-rc.3'];
helpers = helpers || Handlebars.helpers; data = data || {};
  var buffer = "", stack1, stack2, helperMissing=helpers.helperMissing, escapeExpression=this.escapeExpression, functionType="function", self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, options;
  buffer += "\n            <span class=\"share\">";
  options = {hash:{},data:data};
  buffer += escapeExpression(((stack1 = helpers.translate),stack1 ? stack1.call(depth0, "Shared", options) : helperMissing.call(depth0, "translate", "Shared", options)))
    + "</span>\n            ";
  return buffer;
  }

  buffer += "<div class=\"item\">\n    <a class=\"pick\" data-id=\"";
  if (stack1 = helpers.id) { stack1 = stack1.call(depth0, {hash:{},data:data}); }
  else { stack1 = depth0.id; stack1 = typeof stack1 === functionType ? stack1.apply(depth0) : stack1; }
  buffer += escapeExpression(stack1)
    + "\">\n        <img src=\""
    + escapeExpression(((stack1 = ((stack1 = depth0.thumb),stack1 == null || stack1 === false ? stack1 : stack1.url)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" />\n        <p class=\"meta\">";
  if (stack2 = helpers.filename) { stack2 = stack2.call(depth0, {hash:{},data:data}); }
  else { stack2 = depth0.filename; stack2 = typeof stack2 === functionType ? stack2.apply(depth0) : stack2; }
  buffer += escapeExpression(stack2)
    + "<br />\n            <span class=\"details\">";
  if (stack2 = helpers.width) { stack2 = stack2.call(depth0, {hash:{},data:data}); }
  else { stack2 = depth0.width; stack2 = typeof stack2 === functionType ? stack2.apply(depth0) : stack2; }
  buffer += escapeExpression(stack2)
    + " x ";
  if (stack2 = helpers.height) { stack2 = stack2.call(depth0, {hash:{},data:data}); }
  else { stack2 = depth0.height; stack2 = typeof stack2 === functionType ? stack2.apply(depth0) : stack2; }
  buffer += escapeExpression(stack2)
    + "</span>\n            ";
  stack2 = helpers['if'].call(depth0, depth0.shared, {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack2 || stack2 === 0) { buffer += stack2; }
  buffer += "\n        </p>\n    </a>\n</div>\n";
  return buffer;
  })

});