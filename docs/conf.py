from sphinx.highlighting import lexers
from pygments.lexers.web import PhpLexer

extensions = ['sphinx_rtd_theme']
templates_path = ['_templates']
source_suffix = '.rst'
master_doc = 'index'
project = u'Nucleos'
copyright = u'2020, Christian Gripp'
exclude_patterns = ['_build']
highlight_language = 'php'

lexers['php'] = PhpLexer(startinline=True)
lexers['php-annotations'] = PhpLexer(startinline=True)
lexers['php-attributes'] = PhpLexer(startinline=True)
lexers['php-standalone'] = PhpLexer(startinline=True)
lexers['php-symfony'] = PhpLexer(startinline=True)

html_theme = 'sphinx_rtd_theme'
html_static_path = ['_static']
htmlhelp_basename = 'doc'
