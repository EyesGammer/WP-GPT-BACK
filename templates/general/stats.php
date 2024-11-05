<?php

use controllers\Options;
use controllers\Client;

$stats_content = do_hook( 'stats-content' );
$chart_title = do_hook( 'stats-chart-title' );
$disable_y = do_hook( 'stats-chart-no-y' ) ?? false;

$total_price = 0;
if( ! empty( $stats_content ) ) {
    $total_price = array_reduce( $stats_content, function( $sum, $item ) {
        return $sum += floatval( $item[ 'stats' ][ 'price' ] );
    }, 0 );
}

$limit = do_hook( 'stats-limit' ) ?? 30;
$stats_content = array_splice( $stats_content, 0, $limit );

$dates = array();
for( $i = 0; $i <= 9; $i++ ) {
    $dates[] = array(
        'date' => date( 'd', ( $temp_date = strtotime( str_replace( 'x', $i, 'now - x day' ) ) ) )
            . '-'
            . Client::getMonthFRAbrevByPos( intval( date( 'n', $temp_date ) ) )
            . '-'
            . date( 'Y', $temp_date ),
        'sql_date' => date( 'Y-m-d', $temp_date )
    );
}

$config_stats_chart = array(
    'x' => array_reverse( array_map( fn( $x ) => $x[ 'date' ], $dates ) ),
    'delay' => 150,
    'duration' => 300,
    'values' => array()
);
$max = 0;
$min = null;
foreach( $dates as $loop_date ) {
    $temp_in = 0;
    $temp_out = 0;
    foreach( $stats_content as $loop_stat ) {
        if( preg_match( '/' . $loop_date[ 'sql_date' ] . '/i', $loop_stat[ 'stats' ][ 'date' ] ) ) {
            $temp_in += intval( $loop_stat[ 'stats' ][ 'tokens_in' ] );
            $temp_out += intval( $loop_stat[ 'stats' ][ 'tokens_out' ] );
            $temp_max = ( $a = intval( $loop_stat[ 'stats' ][ 'tokens_in' ] ) ) > ( $b = intval( $loop_stat[ 'stats' ][ 'tokens_out' ] ) ) ? $a : $b;
            if( $temp_max > $max ) $max = $temp_max;
            $temp_min = ( $a = intval( $loop_stat[ 'stats' ][ 'tokens_in' ] ) ) < ( $b = intval( $loop_stat[ 'stats' ][ 'tokens_out' ] ) ) ? $a : $b;
            if( $min === null || $temp_min < $min ) $min = $temp_min;
        }
    }
    $max = $temp_in > $temp_out ? max( $temp_in, $max ) : max( $temp_out, $max );
    $min = $temp_in < $temp_out ? min( $temp_in, $min ) : min( $temp_out, $min );
    $config_stats_chart[ 'values' ][] = array(
        'in' => $temp_in,
        'out' => $temp_out,
        'date' => $loop_date
    );
}

$diff = intval( $max ) - intval( $min );
$diff_5 = $diff * 0.05;
$min_5 = $min - $diff_5;
$max_5 = $max + $diff_5;
$bas = $min_5 > 0 ? floor( $min_5 / 10 ) * 10 : 0;
$haut = ceil( $max_5 / 10 ) * 10;
$new_diff = $haut - $bas;
$barre_1 = $bas + $new_diff * 0.25;
$barre_2 = $bas + $new_diff * 0.50;
$barre_3 = $bas + $new_diff * 0.75;

$config_stats_chart[ 'y' ] = array( $bas, $barre_1, $barre_2, $barre_3, $haut );//array( 0, intval( $max / 4 ), intval( $max / 2 ), $max );
$old_values = array();
foreach( $config_stats_chart[ 'values' ] as $loop_value ) {
    $old_values[ $loop_value[ 'date' ][ 'sql_date' ] ] = $loop_value;
}
$config_stats_chart[ 'old_values' ] = $old_values;
$config_stats_chart[ 'values' ] = array_reverse( array_map( fn( $x ) => array_map( fn( $y ) => is_int( $y ) && $max !== 0 ? $y / $max : $y, $x ), $config_stats_chart[ 'values' ] ) );
$config_stats_chart[ 'no_y' ] = $disable_y;

$general_in = Options::getOption( 'general_in' );
$general_out = Options::getOption( 'general_out' );
if( empty( $general_in ) ) $general_in = 0.0005;
else $general_in = floatval( unserialize( $general_in[ 'value' ] ) );
if( empty( $general_out ) ) $general_out = 0.0005;
else $general_out = floatval( unserialize( $general_out[ 'value' ] ) );
?>
<div class="flex flex-col gap-4">
    <div class="flex items-center justify-between">
        <h2 class="text-3xl font-semibold"><?= do_hook( 'stats-title' ) ?? 'Statistiques globales' ?></h2>
    </div>
    <div class="flex flex-col gap-4">
        <div class="w-full h-full rounded-md shadow-md border border-gray-300 flex flex-col">
            <!--
            <div kumo-stats-config="<?= htmlspecialchars( json_encode( $config_stats_chart ) ) ?>" class="w-full h-full flex flex-col-reverse p-6 relative min-h-[320px]">
                <div class="absolute w-full p-4 top-0 left-0" style="height:90%;">
                    <div id="stats-y" class="w-full h-full flex flex-col-reverse justify-between absolute top-0 left-0 py-4 px-2"></div>
                    <div id="stats-values" class="w-full h-full grid grid-cols-10 items-end gap-2 absolute top-0 left-0 pl-9 pr-2 py-7" style="grid-template-columns: repeat(10,minmax(0,1fr));"></div>
                </div>
                <div id="stats-x" class="[&>*]:text-xs [&>*]:mt-2 [&>*]:w-fit [&>*]:text-neutral-300/80 [&>*]:origin-bottom [&>*]:flex [&>*]:justify-end [&>*]:-rotate-45 [&>*]:text-center w-full grid grid-cols-10 items-start gap-2 relative top-0 left-0 pl-3" style="grid-template-columns: repeat(10,minmax(0,1fr));height:10%;"></div>
            </div>
            <?php
            if( ! empty( $chart_title ) ) {
                ?>
                <div class="h-fit w-full text-center">
                    <h2 class="py-2 font-semibold text-2xl"><?= $chart_title ?></h2>
                </div>
                <?php
            }
            ?>
            -->
            <div kumo-stats-config="<?= htmlspecialchars( json_encode( $config_stats_chart ) ) ?>">
                <div class="relative w-full h-full flex-1 flex p-6 min-h-[320px]">
                    <div id="stats-y" class="w-full h-full flex flex-col-reverse justify-between absolute top-0 left-0 py-4 px-2"></div>
                    <div id="stats-values" class="w-full h-full grid grid-cols-10 items-end gap-2 absolute top-0 left-0 pl-9 pr-2 py-7" style="grid-template-columns: repeat(10,minmax(0,1fr));"></div>
                </div>
                <div id="stats-x" class="[&>*]:text-xs [&>*]:mt-2 [&>*]:w-fit [&>*]:text-neutral-300/80 [&>*]:origin-bottom [&>*]:flex [&>*]:justify-end [&>*]:-rotate-45 [&>*]:text-center pb-6 w-full grid grid-cols-10 items-start gap-2 relative top-0 left-0 pl-3" style="grid-template-columns: repeat(10,minmax(0,1fr));height:10%;"></div>
            </div>
        </div>
        <script>
            ( stats_element => {
                const config = JSON.parse( stats_element.getAttribute( 'kumo-stats-config' ) );
                let duration = 300;
                if( config.hasOwnProperty( 'duration' ) ) duration = parseInt( config.duration );
                let delay = 0;
                if( config.hasOwnProperty( 'delay' ) ) delay = parseInt( config.delay );
                const generateRandomString = ( length=5 ) => Array.from( Array( length ), () => Math.floor( Math.random() * 62 ) ).map( char => ( char < 26 ? char + 65 : char < 52 ? char + 26 : char + 4 ) ) .join('');
                const create_tool_tip = ( text ) => {
                    const span_container = document.createElement( 'span' );
                    span_container.innerText = text;
                    span_container.className = "transition duration-300 ease-in-out group-hover:visible invisible w-fit px-4 py-2 bg-black text-white rounded-md absolute z-10 right-[110%] after::content[''] after:absolute after:top-1/2 after:left-full border-4 border-[transparent_transparent_transparent_black] text-xs";
                    return span_container;
                };
                const create_stats_value = ( tokens_in, tokens_out=null, base=null ) => {
                    if(
                        tokens_in === 0 ||
                        (
                            tokens_out !== null &&
                            tokens_out === 0
                        )
                    ) return document.createElement( 'div' );
                    const temp_id = generateRandomString();
                    const div_container = document.createElement( 'div' );
                    div_container.className = "h-full w-full flex flex-row items-end gap-px";
                    const div_in = document.createElement( 'div' );
                    div_in.className = "in-stat w-full rounded-t-md bg-[#b2bec3] transition-[height] ease-in-out" + ( base !== null ? " relative inline-block group text-center" : "" );
                    div_in.dataset.in = tokens_in;
                    div_in.dataset.wout = tokens_out !== null ? 1 : 0;
                    div_in.dataset.id = temp_id;
                    div_in.style.height = '0px';
                    div_in.style.transitionDuration = duration + 'ms';
                    if( base !== null && base.hasOwnProperty( 'in' ) ) {
                        div_in.appendChild( create_tool_tip( base.in ) );
                        /*const span_in = document.createElement( 'span' );
                        span_in.className = "text-white text-sm";
                        span_in.innerText = base.in;
                        div_in.appendChild( span_in );*/
                    }
                    div_container.appendChild( div_in );
                    if( tokens_out !== null ) {
                        const div_out = document.createElement('div');
                        div_out.className = "out-stat w-full rounded-t-md bg-[#636e72] transition-[height] ease-in-out" + ( base !== null ? " relative inline-block group text-center" : "" );
                        div_out.dataset.in = tokens_in;
                        div_out.dataset.out = tokens_out;
                        div_out.dataset.id = temp_id;
                        div_out.style.height = '0px';
                        div_out.style.transitionDuration = duration + 'ms';
                        if( base !== null && base.hasOwnProperty( 'out' ) ) {
                            div_out.appendChild( create_tool_tip( base.out ) );
                            /*const span_out = document.createElement( 'span' );
                            span_out.className = "text-white text-sm";
                            span_out.innerText = base.out;
                            div_out.appendChild( span_out );*/
                        }
                        div_container.appendChild( div_out );
                    }
                    return div_container;
                };
                const create_stats_x = ( text ) => {
                    const span_container = document.createElement( 'span' );
                    span_container.innerText = text;
                    return span_container;
                };
                const create_stats_y = ( text ) => {
                    const span_container = document.createElement( 'span' );
                    span_container.className = 'flex items-center gap-2 text-neutral-300/80 text-sm';
                    span_container.innerText = text;
                    const span_hr = document.createElement( 'hr' );
                    span_hr.className = 'w-full border-t-neutral-300/80';
                    span_container.appendChild( span_hr );
                    return span_container;
                };
                const sleep = async ( ms ) => new Promise( resolve => setTimeout( resolve, ms ) );
                const stats_x = stats_element.querySelector( '#stats-x' );
                const stats_y = stats_element.querySelector( '#stats-y' );
                const stats_values = stats_element.querySelector( '#stats-values' );
                if(
                    config.hasOwnProperty( 'x' ) &&
                    config.hasOwnProperty( 'y' ) &&
                    config.hasOwnProperty( 'values' )
                ) {
                    config.values.forEach( ( item, index ) => {
                        stats_values.appendChild( create_stats_value( item.in, item.out ?? null, config.old_values[ item.date.sql_date ] ?? null ) );
                    } );
                    config.x.forEach( item => {
                        stats_x.appendChild( create_stats_x( item ) );
                    } );
                    if( ! config.no_y ) {
                        config.y.forEach( item => {
                            stats_y.appendChild( create_stats_y( item ) );
                        } );
                    }
                    let in_stats = [...document.querySelectorAll( '.in-stat' )].map( item => {
                        return { key: item.dataset.id, value: item };
                    } ).reduce( ( obj, item ) => Object.assign( obj, {
                        [ item.key ]: [ item.value ]
                    } ), {} );
                    Object.entries( [...document.querySelectorAll( '.out-stat' )].map( item => {
                        return { key: item.dataset.id, value: item };
                    } ).reduce( ( obj, item ) => Object.assign( obj, {
                        [ item.key ]: [ item.value ]
                    } ), {} ) ).forEach( item => {
                        if( in_stats.hasOwnProperty( item[ 0 ] ) ) in_stats[ item[ 0 ] ].push( ...item[ 1 ] );
                        else in_stats[ item[ 0 ] ] = [ ...item[ 1 ] ];
                    } );
                    let stats_array = Object.values( in_stats );
                    setTimeout( () => {
                        stats_array[ 0 ].forEach( item => {
                            if( item.dataset.hasOwnProperty( 'wout' ) ) item.style.height = `calc(100% * ${ item.dataset.in })`;
                            else item.style.height = `calc(100% * ${ item.dataset.out ?? ( 1 - item.dataset.in ) } - 8px)`;
                        } );
                        stats_array.forEach( stat => {
                            sleep( delay ).then( () => {
                                stat.forEach( item => {
                                    if( item.dataset.hasOwnProperty( 'wout' ) ) item.style.height = `calc(100% * ${ item.dataset.in })`;
                                    else item.style.height = `calc(100% * ${ item.dataset.out ?? ( 1 - item.dataset.in ) } - 8px)`;
                                } );
                            } );
                            delay += 80;
                        } );
                    }, 300 ) ;
                }
            } )( document.querySelector( '[kumo-stats-config]' ) );
        </script>
        <div class="w-full rounded-md shadow-md border border-gray-300 flex flex-col gap-4 p-6">
            <div class="flex justify-between items-end">
                <span class="text-xl"><?= do_hook( 'stats-sub-title' ) ?? 'Utilisation globale' ?></span>
                <span class="text-sm text-gray-400"><?= $total_price ?>€</span>
            </div>
            <table>
                <thead>
                    <tr class="[&>*]:font-semibold [&>*]:text-gray-400 [&>*]:py-2 border-b border-gray-300">
                        <th class="text-left">Client</th>
                        <th class="text-right">Date</th>
                        <th class="text-right">Tokens In</th>
                        <th class="text-right">Tokens Out</th>
                        <th class="text-right">Prix Client</th>
                        <th class="text-right">Prix Achat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if( ! empty( $stats_content ) ) foreach( $stats_content as $index => $loop_stats ) {
                    ?>
                    <tr class="[&>:not(:first-child)]:text-right [&>*]:py-2 border-b border-gray-300" data-uuid="<?= $loop_stats[ 'stats' ][ 'uuid' ] ?>">
                        <td class="text-left">
                            <a href="<?= get_url( '/client/' . $loop_stats[ 'stats' ][ 'uuid_client' ] ) ?>" class="underline">
                                <?= $loop_stats[ 'client' ][ 'nom' ] ?>
                            </a>
                        </td>
                        <td><?= date( 'd/m/Y H:i:s', strtotime( $loop_stats[ 'stats' ][ 'date' ] ) ) ?></td>
                        <td><?= $loop_stats[ 'stats' ][ 'tokens_in' ] ?></td>
                        <td><?= $loop_stats[ 'stats' ][ 'tokens_out' ] ?></td>
                        <td><?= number_format( floatval( $loop_stats[ 'stats' ][ 'price' ] ), 8, ',', ' ' ) ?></td>
                        <td><?= number_format( floatval(
                                floatval( $loop_stats[ 'stats' ][ 'tokens_in' ] ) * $general_in + floatval( $loop_stats[ 'stats' ][ 'tokens_out' ] ) * $general_out
                            ), 8, ',', ' ' ) ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>