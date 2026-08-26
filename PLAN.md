# Flexgrid Webshop module plan

## Plaatsing

De module staat los van de Flexgrid core:

```text
Flexgrid/Modules/Webshop/src
```

Namespace:

```text
Flexgrid\Modules\Webshop
```

Projecten kunnen later overrides toevoegen via:

```text
Templates/Webshop
App/Webshop
```

## Fase 1

- Module discovery via Autowire.
- Webshop dashboardpanel in Flexgrid.
- Admin dashboard startpagina.
- Basisplan voor entities, frontend templates en services.
- Eerste product entities: `WebshopProductGroup` en `WebshopProduct`.
- Eerste frontend blocks: productgrid, uitgelichte producten en productgroepgrid.
- Productdetail template via `getDetailUrl($pageId)` en de bestaande detailroute-flow.
- Cart basis met sessie-opslag en add-to-cart via `AjaxEvent`.
- Cart summary en cart page met update/remove/clear via `AjaxEvent`.
- Checkout MVP: customer, order en orderregels opslaan zonder betaalprovider.
- Admin orders overview/detail met status en betaalstatus via `AjaxEvent`.
- Factuurbasis: invoice entities, factuur maken vanuit order en admin factuuroverzicht/detail.
- Rapportagebasis: maand- en jaaroverzicht voor betaalde orders en facturen.
- Betaalbasis: payment transaction entity, pending transactie bij checkout en status-sync naar orders.
- Verzendbasis: shipping method entity, keuze in checkout en verzendkosten op orders.
- Kortingsbasis: discount code entity, validatie in checkout en korting op order/factuur.
- Voorraadbasis: stock mutation entity, checkout voorraadvalidatie en automatisch afboeken.
- BTW-basis: tax rate entity, optionele koppeling op producten/verzendmethodes en btw op verzending.
- Variantbasis: product variant entity, variantkeuze in productdetail/cart en variantdata op orderregels.
- Mailbasis: orderbevestiging naar klant en adminmelding na checkout.
- PDF-factuurbasis: Dompdf generatie, opslag op factuur en admin opnieuw genereren.
- Mollie-basis: betaalmethode in checkout, Mollie redirect, return en webhook sync naar payment transactions.
- Status-flow hardening: centrale statusservice, voorraad terugboeken, factuur annuleren en statusmails.
- Webshop settings: admin scherm voor pagina's, mail, betaling en factuurinstellingen.
- Fulfillment-basis: verzonden-status, track & trace velden op orders, admin opslag en verzendmail.
- Klantenbeheer-basis: webshop admin klantenoverzicht, klantdetail en orderhistorie per klant.
- Admin filters: zoeken/filteren op bestellingen en klanten in de webshop backoffice.
- Productbeheer-basis: webshop admin productenoverzicht met filters en productdetail met AjaxFields.
- AI productgeneratie: product-startscherm met titel, fabrikant/merk, inkoopprijs, marge-instelling, optionele web search en credit-afschrijving via OpenAI Responses.
- AI productgeneratie: meerdere productafbeeldingen uploaden, alle afbeeldingen als AI-context gebruiken en automatisch opslaan als hoofdafbeelding plus multiple images.
- AI productgeneratie: optionele nieuwe productgroep voorstellen, inkoopprijs schatten, titel aanvullen op basis van afbeelding en minimale omschrijving-alinea's.
- AI variantdetectie: optioneel kleur/maat-varianten herkennen uit meerdere afbeeldingen, meerdere producten aanmaken en automatisch als siblings koppelen.
- Frontend polish: losse AddToCart template met hoeveelheid, verbeterde productdetail en cart summary mini-cart.
- Frontend polish: productkaarten met voorraadlabel, consistente prijsweergave en compacte AddToCart.
- Frontend polish: winkelwagenpagina met betere empty-state, regelprijzen, subtotaalnotitie en winkel/checkout acties.
- Checkout polish: duidelijkere Mollie/iDEAL/Wero betaalmethode en Mollie return via `getPaymentReturnUrl`.
- Checkout polish: formulier opgedeeld in contact, adres, extra, verzending en betaling met duidelijkere submit- en betaaltekst.
- Checkout polish: interactieve summary voor gekozen verzending, betaalmethode, kortingscode-hint en geschat totaal.
- Payment return polish: duidelijke statuspagina na Mollie met betaalgegevens en vervolgacties.
- Frontend polish: lege states voor productgrid/productgroepen/checkout en consistente checkout feedbackstijl.
- Productdetail polish: image fallback, beschikbaarheidsmelding, specs en teruglink naar overzicht.
- Hoofdgroepen/subgroepen: `WebshopProductMainGroup` boven `WebshopProductGroup`, zodat collecties zoals Wonen/Kleding/Accessoires meerdere productgroepen kunnen bundelen.
- Hoofdgroep landingspagina: `WebshopProductMainGroup` uitgebreid met intro, CTA en highlight velden. De frontend wordt bewust opgeknipt in losse template-methods (`Banner`, `ProductGroups`, `Cta`, `Slider`, `Highlight`) in plaats van een monolithische detailtemplate, zodat projecten eenvoudig per blok maatwerk kunnen injecteren of vervangen.

## MVP entities

- WebshopProduct
- WebshopProductMainGroup
- WebshopProductGroup
- WebshopOrder
- WebshopOrderLine
- WebshopCustomer
- WebshopPaymentTransaction
- WebshopInvoice
- WebshopInvoiceLine
- WebshopShippingMethod
- WebshopDiscountCode
- WebshopStockMutation
- WebshopTaxRate

## Later entities

- WebshopProductImage

## Frontend templates

- ProductGrid
- ProductCard
- ProductDetail
- FeaturedProducts
- ProductGroupGrid
- ProductMainGroupLanding/Banner
- ProductMainGroupLanding/ProductGroups
- ProductMainGroupLanding/Cta
- ProductMainGroupLanding/Slider
- ProductMainGroupLanding/Highlight
- CartSummary
- CartPage
- CheckoutPage
- OrderSuccessPage

## Backend templates

- Dashboard
- ProductsOverview
- OrdersOverview
- OrderDetail
- InvoicesOverview
- InvoiceDetail
- Reports
- Settings

## Services

- CartService
- PriceService
- CheckoutService
- OrderService
- PaymentService
- MolliePaymentService
- InvoiceService
- InvoicePdfService
- OrderMailService
- OrderStatusService
- OrderStatsService
- RevenueReportService
- ProductAvailabilityService
