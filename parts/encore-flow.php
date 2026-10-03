<?php
/**
 * Encore "how it works" illustration: Airtable row → Publish → website.
 * Pure HTML/CSS, decorative (hidden from screen readers — the steps are in
 * the text beside it).
 *
 * @package Drift_Create
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="flow" aria-hidden="true">
	<div class="flow__panel flow__table">
		<p class="flow__label">Airtable · Gigs</p>
		<div class="flow__row flow__row--head"><span>Date</span><span>Venue</span><span>Status</span></div>
		<div class="flow__row"><span>14 Nov</span><span>Phoenix, Exeter</span><span class="tag tag--green">On Sale</span></div>
		<div class="flow__row flow__row--new"><span>20 Nov</span><span>The Fleece, Bristol</span><span class="tag tag--amber">Few Left</span></div>
		<div class="flow__row"><span>27 Nov</span><span>The Joiners, Soton</span><span class="tag tag--green">On Sale</span></div>
	</div>
	<div class="flow__publish"><span class="flow__btn">Publish website</span></div>
	<div class="flow__panel flow__site">
		<p class="flow__label">the-velvet-tides.co.uk</p>
		<p class="flow__band">The Velvet Tides</p>
		<div class="flow__gig"><b>14</b><span>Phoenix<small>Exeter</small></span><i>Tickets</i></div>
		<div class="flow__gig flow__gig--new"><b>20</b><span>The Fleece<small>Bristol</small></span><i>Tickets</i></div>
		<div class="flow__gig"><b>27</b><span>The Joiners<small>Southampton</small></span><i>Tickets</i></div>
	</div>
</div>
