import { $, clone } from "./util";
import { DEFAULT_RESULT_LIMIT } from "./conf";
import EXAMPLES from "./examples";
import Query from "./query";
import typeahead from "./typeahead";
import Vue from "vue";

function parseWhere(str) {
    var parts = str.split(" ");
    return { has : 'where', property : parts[0], value : parts[1] };
}

function createEmptyRule() {
    return {
        has : 'where',
        property : null,
        value : null
    };
};

class View {
    constructor(selector) {
        this.selector = $(selector);
        this.query = new Query();
        this.setup();
    }

    parseResult(result) {
        if (result.image) {
            result.thumb = result.image.value + '?width=300';
        }

        if (result.item) {
            result.id = result.item.value.replace('http://www.wikidata.org/entity/', '');
        }

        return result;
    }

    setup() {
        var self = this;

        this.view = new Vue({
            el : this.selector,

            components : { typeahead },

            data : {
                state : 'search',

                results : false,

                hasOptions : [
                    { value : 'where', label : 'have' },
                    { value : 'minus', label : "don't have" }
                ],

                query : null,

                rules : [
                    {
                        has : 'where'
                    }
                ],

                error : false,

                withimages : true,

                limit : DEFAULT_RESULT_LIMIT,

                loading : false,

                examples : EXAMPLES.map(function(e) {
                    e.data = e.data.map(parseWhere);
                    return e;
                })
            },

            methods : {
                addRule : function() {
                    this.rules.push(createEmptyRule());
                },

                doQuery : function() {
                    this.results = [];

                    this.loading = true;

                    var rules = clone(this.rules);

                    if (this.withimages) {
                        rules.push({
                            has : 'image'
                        });
                    }

                    rules.limit = this.limit;

                    this.query = self.query.build(rules);

                    self.query.fetch(this.query, function(results) {
                        this.results = results.map(self.parseResult);
                        this.loading = false;
                    }.bind(this));
                },

                removeRule : function(rule) {
                    this.rules = this.rules.filter(function(r) {
                        return r !== rule;
                    });
                },

                setExample : function(example) {
                    this.rules = example.data;
                }
            }
        });
    }
};

export default View;