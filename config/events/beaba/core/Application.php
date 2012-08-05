<?php
return array(
    \beaba\core\Application::E_LOAD => array(
        function( \beaba\core\Application $sender, $args ) {
            $sender->getInfos()->setConfig(
                'APP_START', microtime(true)
            );
        }
    ),
    \beaba\core\Application::E_AFTER_RENDER => array(
        function( \beaba\core\Application $sender, $args ) {
            $body = strpos( $args['response'], '</body>');
            if ( $body !== false ) {
                $start = $sender->getInfos()->getConfig('APP_START');
                $interval =  round(microtime(true) - $start, 4);
                $self = $sender->getPlugins()->getPlugin('speed-track');
                if ( $interval > $self->getOption('limit-warn') ) 
                {
                    if ( $interval > $self->getOption('limit-error') ) 
                    {
                        // inserting logs
                        $args['response'] = 
                            substr($args['response'], 0, $body)
                            . '<div class="error">'
                            . '<h1>ERROR : </h1>Page duration : ' .$interval. ' sec'
                            . '</div>' . "\n"
                            . substr($args['response'], $body)
                        ;
                    } else {
                        // inserting logs
                        $args['response'] = 
                            substr($args['response'], 0, $body)
                            . '<div class="logs">'
                            . 'Warning : Page duration : ' .$interval. ' sec'
                            . '</div>' . "\n"
                            . substr($args['response'], $body)
                        ;
                    }
                }
            }
        }
    )
);