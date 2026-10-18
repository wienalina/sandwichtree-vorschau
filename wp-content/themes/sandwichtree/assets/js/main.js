/**
 * Sandwichtree – Menü und Suche auf- und zuklappen.
 */
( function () {
	'use strict';

	var header = document.getElementById( 'site-header' );
	if ( ! header ) {
		return;
	}

	var navToggle = header.querySelector( '.nav-toggle' );
	var searchToggle = header.querySelector( '.search-toggle' );
	var search = document.getElementById( 'site-search' );

	if ( navToggle ) {
		navToggle.addEventListener( 'click', function () {
			var open = header.classList.toggle( 'is-open' );
			navToggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		} );
	}

	if ( searchToggle && search ) {
		searchToggle.addEventListener( 'click', function () {
			var willOpen = search.hasAttribute( 'hidden' );
			if ( willOpen ) {
				search.removeAttribute( 'hidden' );
				var input = search.querySelector( 'input[type="search"]' );
				if ( input ) {
					input.focus();
				}
			} else {
				search.setAttribute( 'hidden', '' );
			}
			searchToggle.setAttribute( 'aria-expanded', willOpen ? 'true' : 'false' );
		} );
	}

	// Escape schließt Menü und Suche.
	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' !== event.key ) {
			return;
		}
		if ( header.classList.contains( 'is-open' ) && navToggle ) {
			header.classList.remove( 'is-open' );
			navToggle.setAttribute( 'aria-expanded', 'false' );
			navToggle.focus();
		}
		if ( search && ! search.hasAttribute( 'hidden' ) && searchToggle ) {
			search.setAttribute( 'hidden', '' );
			searchToggle.setAttribute( 'aria-expanded', 'false' );
			searchToggle.focus();
		}
	} );
} )();
