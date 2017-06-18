<?php
    require '../../lib/class-hay.php';

    $hay = new Hay("vizquery", [
        "styles" => [ 'style.css' ],
        "scripts" => [
            'dist.js'
        ]
    ]);

    $hay->header();
?>
    <div id="app" v-cloak>
        <h1>
            <a href="<?= $hay->getUrl(); ?>">Wikidata <?php $hay->title(); ?></a>
        </h1>

        <p class="lead" v-show="!hadResults">
            <?php $hay->description(); ?>
        </p>

        <p class="intro" v-show="!hadResults">This tool allows you to query Wikidata, the database of all things in the world. For example, you could get a list of world heritage sites in your country. A list with movies with Joe Pesci and Robert De Niro. Or female trumpet players. You construct a query by combining <em>properties</em> and <em>items</em> into <em>claims</em>. Let's start with a simple example: <a v-bind:href="'#' + encodeURIComponent(examples[0].query)">click here to find all cats on Wikidata</a>.

        </p>

        <div class="alert alert-danger" v-show="error">
            Sorry, something went wrong. Either your query was wrong, or there were no results.
            <p v-if="error">{{error}}</p>
        </div>

        <div class="form">
            <h3>Select items where...</h3>

            <section v-for="triple in query.triples"
                     v-bind:key="query.hashTriple(triple)">
                <subject-entry
                    v-model="triple.subject"
                    v-bind:subjects="query.subjects"></subject-entry>

                <p>has a property</p>

                <entity-entry
                    type="property"
                    v-bind:minlength="2"
                    v-model="triple.predicate"></entity-entry>

                <p>that is</p>

                <entity-entry
                    type="item"
                    v-bind:minlength="2"
                    v-model="triple.object"></entity-entry>

                <button class="btn btn-default" v-on:click="query.removeTriple(triple)">
                    <span class="glyphicon glyphicon-minus"></span>
                    Remove rule
                </button>
            </section>

            <section>
                <button class="btn btn-default" v-on:click="addRule">
                    <span class="glyphicon glyphicon-plus"></span>
                    Add rule
                </button>
            </section>

            <section>
                <label for="limit">Maximum results (0 is no limit)</label>
                <input type="number" id="limit" v-model="query.limit">
            </section>

            <section>
                <button class="btn btn-primary"
                        v-on:click="doQuery"
                        v-bind:disabled="!query.triples || !query.triples.length">
                    <span class="glyphicon glyphicon-search"></span>
                    Query
                </button>
            </section>
        </div>

        <div class="alert alert-info" v-show="loading">
            Loading...
        </div>

        <div class="alert alert-info" v-show="results.length == 0 && !loading && hadResults">
            No results
        </div>

        <div class="results" v-show="results.length">
            <h3 v-show="results">
                Results <small>{{results.length}}</small>

                <div class="btn-group pull-right" role="group">
                    <button type="button"
                            class="btn btn-default"
                            v-bind:class="{ active : display === 'table' }"
                            v-on:click="setDisplay('table')">
                        <span class="glyphicon glyphicon-list"></span>
                        Table
                    </button>

                    <button type="button"
                            class="btn btn-default"
                            v-bind:class="{ active : display === 'grid' }"
                            v-on:click="setDisplay('grid')">
                        <span class="glyphicon glyphicon-th"></span>
                        Grid
                    </button>
                </div>
            </h3>

            <p>
                <a v-bind:href="csv" download="data.csv">Download as CSV</a>
            </p>

            <display-table v-if="display === 'table'" v-bind:data="results"></display-table>

            <display-grid v-if="display === 'grid'" v-bind:data="results"></display-grid>
        </div>

        <div v-show="hadResults">
            <h3>
                <a v-bind:href="'https://query.wikidata.org/#' + encodeURIComponent(queryString)"
                   target="_blank">
                    SPARQL Query
                </a>
            </h3>

            <details>
                <summary>Show query</summary>
                <pre>{{queryString}}</pre>
            </details>
        </div>

        <h3>Example queries</h3>

        <ul>
            <li v-for="e in examples">
                <a v-bind:href="'#' + encodeURIComponent(e.query)">{{e.description}}</a>
            </li>
        </ul>
    </div>
<?php
    $hay->footer();
?>