const elixir = require('laravel-elixir');

elixir.config.css.minifier.pluginOptions = {
  compatibility: 'ie7',
  keepSpecialComments: 0
};
elixir.config.sourcemaps = false;

Elixir.extend('rev', function(src) {
  Elixir.mixins.exec(
    'php artisan holly:assets',
    new Elixir.GulpPaths().src(src, this.config.publicPath).src.path
  );
});

elixir((mix) => {

  mix.copy(
      'node_modules/font-awesome/fonts',
      'public/fonts'
    )
    .copy([
        'node_modules/holly-packages/admin-lte/dist/img',
        'node_modules/holly-packages/lightbox2/dist/img'
      ],
      'public/img'
    )
    .copy(
      'node_modules/holly-packages/icheck/img/icheck/square',
      'public/img/icheck/square'
    )
    .copy(
      'node_modules/holly-packages/utilities/ie-compatible.min.js',
      'public/js/ie-compatible.js'
    )
    // Admin
    .sass('admin.scss')
    .scripts(getScripts([
        './node_modules/holly-packages/admin-lte/dist/js/AdminLTE.js',
        './node_modules/jquery-slimscroll/jquery.slimscroll.js',
        './node_modules/holly-packages/datatables/dist/js/dataTables-responsive-bs.js',
        'dataTable.defaults.js',
        './node_modules/blueimp-md5/js/md5.js'
      ]),
      'public/js/admin.js'
    )
    // Website
    .sass('site.scss')
    .scripts(getScripts([
        './node_modules/holly-packages/utilities/JSBridge.js'
      ]),
      'public/js/site.js'
    )
    // Revisioning assets
    .rev(['css', 'js', 'img']);

});

function getScripts(scripts) {
  return [
    './node_modules/jquery/dist/jquery.js',
    './node_modules/bootstrap/dist/js/bootstrap.js',
    './node_modules/holly-packages/utilities/csrf_token.js',
    './node_modules/holly-packages/utilities/captcha.js',
    './node_modules/holly-packages/utilities/captcha_link.js',
    './node_modules/holly-packages/fastclick/dist/fastclick.js',
    './node_modules/holly-packages/bootbox/dist/bootbox.js',
    './node_modules/bootnotify/bootnotify.js',
    './node_modules/spin.js/spin.js',
    './node_modules/spin.js/jquery.spin.js',
    './node_modules/jquery-form/jquery.form.js',
    './node_modules/holly-packages/icheck/js/icheck.js',
    './node_modules/holly-packages/icheck/icheck-demo.js',
    './node_modules/holly-packages/lightbox2/dist/js/lightbox.js'
  ].concat(scripts);
}
