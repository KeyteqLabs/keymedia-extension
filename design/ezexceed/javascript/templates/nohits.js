define(['handlebars'], function(Handlebars) {

return Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [2,'>= 1.0.0-rc.3'];
helpers = helpers || Handlebars.helpers; data = data || {};
  var buffer = "", stack1, options, helperMissing=helpers.helperMissing, escapeExpression=this.escapeExpression;


  buffer += "<div class=\"well well-large\">\n    <h2>";
  options = {hash:{},data:data};
  buffer += escapeExpression(((stack1 = helpers.translate),stack1 ? stack1.call(depth0, "No results", options) : helperMissing.call(depth0, "translate", "No results", options)))
    + "</h2>\n</div>\n";
  return buffer;
  })

});