<?php

use App\Livewire\Manta\Agenda\AgendaCreate;
use App\Livewire\Manta\Agenda\AgendaList;
use App\Livewire\Manta\Agenda\AgendaMaps;
use App\Livewire\Manta\Agenda\AgendaRead;
use App\Livewire\Manta\Agenda\AgendaUpdate;
use App\Livewire\Manta\Agenda\AgendaUpload;
use App\Livewire\Manta\Chatgpt\ChatgptChat;
use App\Livewire\Manta\Cms\CmsNumbers;
use App\Livewire\Manta\Cms\CmsOptions;
use App\Livewire\Manta\Cms\CmsSandbox;
use App\Livewire\Manta\Contact\ContactCreate;
use App\Livewire\Manta\Contact\ContactList;
use App\Livewire\Manta\Contact\ContactRead;
use App\Livewire\Manta\Contact\ContactSettings;
use App\Livewire\Manta\Contact\ContactUpdate;
use App\Livewire\Manta\Contact\ContactUpload;
use App\Livewire\Manta\Contactperson\ContactpersonCreate;
use App\Livewire\Manta\Contactperson\ContactpersonList;
use App\Livewire\Manta\Contactperson\ContactpersonRead;
use App\Livewire\Manta\Contactperson\ContactpersonUpdate;
use App\Livewire\Manta\Contactperson\ContactpersonUpload;
use App\Livewire\Manta\Faq\FaqCreate;
use App\Livewire\Manta\Faq\FaqList;
use App\Livewire\Manta\Faq\FaqRead;
use App\Livewire\Manta\Faq\FaqUpdate;
use App\Livewire\Manta\House\HouseCreate;
use App\Livewire\Manta\House\HouseList;
use App\Livewire\Manta\House\HouseMaps;
use App\Livewire\Manta\House\HouseRead;
use App\Livewire\Manta\House\HouseUpdate;
use App\Livewire\Manta\House\HouseUpload;
use App\Livewire\Manta\Menu\MenuCreate;
use App\Livewire\Manta\Menu\MenuItems;
use App\Livewire\Manta\Menu\MenuList;
use App\Livewire\Manta\Menu\MenuRead;
use App\Livewire\Manta\Menu\MenuUpdate;
use App\Livewire\Manta\Menu\MenuUploads;
use App\Livewire\Manta\Menuitem\MenuitemRead;
use App\Livewire\Manta\Menuitem\MenuitemUpdate;
use App\Livewire\Manta\Page\PageCreate;
use App\Livewire\Manta\Page\PageList;
use App\Livewire\Manta\Page\PageRead;
use App\Livewire\Manta\Page\PageUpdate;
use App\Livewire\Manta\Page\PageUpload;
use App\Livewire\Manta\Staff\StaffCreate;
use App\Livewire\Manta\Staff\StaffList;
use App\Livewire\Manta\Staff\StaffUpdate;
use App\Livewire\Manta\Product\ProductCreate;
use App\Livewire\Manta\Product\ProductList;
use App\Livewire\Manta\Product\ProductRead;
use App\Livewire\Manta\Product\ProductUpdate;
use App\Livewire\Manta\Product\ProductUpload;
use App\Livewire\Manta\Productcat\ProductcatCreate;
use App\Livewire\Manta\Productcat\ProductcatTree;
use App\Livewire\Manta\Productcat\ProductcatRead;
use App\Livewire\Manta\Productcat\ProductcatUpdate;
use App\Livewire\Manta\Productcat\ProductcatUpload;
use App\Livewire\Manta\Upload\UploadCreate;
use App\Livewire\Manta\Upload\UploadCrop;
use App\Livewire\Manta\Upload\UploadList;
use App\Livewire\Manta\Upload\UploadRead;
use App\Livewire\Manta\Upload\UploadUpdate;
use App\Livewire\Manta\User\UserCreate;
use App\Livewire\Manta\User\UserList;
use App\Livewire\Manta\User\UserRead;
use App\Livewire\Manta\User\UserUpdate;
use App\Livewire\Manta\News\NewsCreate;
use App\Livewire\Manta\News\NewsList;
use App\Livewire\Manta\News\NewsRead;
use App\Livewire\Manta\News\NewsUpdate;
use App\Livewire\Manta\News\NewsUpload;
use App\Livewire\Manta\Newscat\NewscatCreate;
use App\Livewire\Manta\Newscat\NewscatList;
use App\Livewire\Manta\Newscat\NewscatRead;
use App\Livewire\Manta\Newscat\NewscatUpdate;
use App\Livewire\Manta\Newscat\NewscatUpload;
use App\Livewire\Manta\Photoalbum\PhotoalbumCreate;
use App\Livewire\Manta\Photoalbum\PhotoalbumList;
use App\Livewire\Manta\Photoalbum\PhotoalbumRead;
use App\Livewire\Manta\Photoalbum\PhotoalbumUpdate;
use App\Livewire\Manta\Photoalbum\PhotoalbumUpload;
use App\Livewire\Manta\Product\ProductCategory;
use App\Livewire\Manta\Productcat\ProductcatList;
use App\Livewire\Manta\Translator\TranslatorList;
use App\Livewire\Manta\Translator\TranslatorUpdate;
use App\Livewire\Manta\Routeseo\RouteseoCreate;
use App\Livewire\Manta\Routeseo\RouteseoList;
use App\Livewire\Manta\Routeseo\RouteseoRead;
use App\Livewire\Manta\Routeseo\RouteseoUpdate;
use App\Livewire\Manta\Routeseo\RouteseoUpload;
use App\Livewire\Manta\Staff\StaffRead;
use Manta\Models\News;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'cms', 'middleware' => ['auth:staff', 'web']], function () {
    Route::get('/dashboard', ProductList::class)->name('cms.dashboard');

    Route::get('/sandbox', CmsSandbox::class)->name('cms.sandbox');
    Route::get('/numbers', CmsNumbers::class)->name('cms.numbers');
    Route::get('/instellingen', CmsOptions::class)->name('cms.options');

    Route::get('/talen', TranslatorList::class)->name('translator.list');
    Route::get('/talen/update', TranslatorUpdate::class)->name('translator.update');

    Route::get('/chatgpt/chat', ChatgptChat::class)->name('chatgpt.chat');


    /**
     * * Agenda
     */
    Route::get('/agenda', AgendaList::class)->name('agenda.list');
    Route::get('/agenda/toevoegen', AgendaCreate::class)->name('agenda.create');
    Route::get('/agenda/aanpassen/{agenda}', AgendaUpdate::class)->name('agenda.update');
    Route::get('/agenda/lezen/{agenda}', AgendaRead::class)->name('agenda.read');
    Route::get('/agenda/bestanden/{agenda}', AgendaUpload::class)->name('agenda.uploads');
    Route::get('/agenda/maps/{agenda}', AgendaMaps::class)->name('agenda.maps');

    /**
     * * Contact
     */
    Route::get('/contact', ContactList::class)->name('contact.list');
    Route::get('/contact/toevoegen', ContactCreate::class)->name('contact.create');
    Route::get('/contact/aanpassen/{contact}', ContactUpdate::class)->name('contact.update');
    Route::get('/contact/lezen/{contact}', ContactRead::class)->name('contact.read');
    Route::get('/contact/bestanden/{contact}', ContactUpload::class)->name('contact.uploads');
    Route::get('/contact/instellingen', ContactSettings::class)->name('contact.settings');

    /**
     * * Contactperson
     */
    Route::get('/contactpersonen', ContactpersonList::class)->name('contactperson.list');
    Route::get('/contactpersonen/toevoegen', ContactpersonCreate::class)->name('contactperson.create');
    Route::get('/contactpersonen/aanpassen/{contactperson}', ContactpersonUpdate::class)->name('contactperson.update');
    Route::get('/contactpersonen/lezen/{contactperson}', ContactpersonRead::class)->name('contactperson.read');
    Route::get('/contactpersonen/bestanden/{contactperson}', ContactpersonUpload::class)->name('contactperson.upload');

    /**
     * * Faq
     */
    Route::get('/veelgestelde-vragen', FaqList::class)->name('faq.list');
    Route::get('/veelgestelde-vragen/toevoegen', FaqCreate::class)->name('faq.create');
    Route::get('/veelgestelde-vragen/aanpassen/{faq}', FaqUpdate::class)->name('faq.update');
    Route::get('/veelgestelde-vragen/lezen/{faq}', FaqRead::class)->name('faq.read');

    /**
     * * Houses
     */
    Route::get('/huizen', HouseList::class)->name('house.list');
    Route::get('/huizen/toevoegen', HouseCreate::class)->name('house.create');
    Route::get('/huizen/aanpassen/{house}', HouseUpdate::class)->name('house.update');
    Route::get('/huizen/lezen/{house}', HouseRead::class)->name('house.read');
    Route::get('/huizen/bestanden/{house}', HouseUpload::class)->name('house.uploads');
    Route::get('/huizen/maps/{house}', HouseMaps::class)->name('house.maps');

    /**
     * * Menu
     */
    Route::get('/menu', MenuList::class)->name('menu.list');
    Route::get('/menu/toevoegen', MenuCreate::class)->name('menu.create');
    Route::get('/menu/aanpassen/{menu}', MenuUpdate::class)->name('menu.update');
    Route::get('/menu/lezen/{menu}', MenuRead::class)->name('menu.read');
    Route::get('/menu/bestanden/{menu}', MenuUploads::class)->name('menu.upload');
    Route::get('/menu/items/{menu}', MenuItems::class)->name('menu.items');

    Route::get('/menuitem/aanpassen/{menuitem}', MenuitemUpdate::class)->name('menuitem.update');
    Route::get('/menuitem/lezen/{menuitem}', MenuitemRead::class)->name('menuitem.read');

    /**
     * * News
     */
    Route::get('/nieuws', NewsList::class)->name('news.list');
    Route::get('/nieuws/toevoegen', NewsCreate::class)->name('news.create');
    Route::get('/nieuws/aanpassen/{news}', NewsUpdate::class)->name('news.update');
    Route::get('/nieuws/lezen/{news}', NewsRead::class)->name('news.read');
    Route::get('/nieuws/bestanden/{news}', NewsUpload::class)->name('news.upload');

    Route::get('/nieuws/categorieen', NewscatList::class)->name('newscat.list');
    Route::get('/nieuws/categorieen/toevoegen', NewscatCreate::class)->name('newscat.create');
    Route::get('/nieuws/categorieen/aanpassen/{newscat}', NewscatUpdate::class)->name('newscat.update');
    Route::get('/nieuws/categorieen/lezen/{newscat}', NewscatRead::class)->name('newscat.read');
    Route::get('/nieuws/categorieen/bestanden/{newscat}', NewscatUpload::class)->name('newscat.upload');

    /**
     * * Photoalbum
     */
    Route::get('/fotoalbum', PhotoalbumList::class)->name('photoalbum.list');
    Route::get('/fotoalbum/toevoegen', PhotoalbumCreate::class)->name('photoalbum.create');
    Route::get('/fotoalbum/aanpassen/{photoalbum}', PhotoalbumUpdate::class)->name('photoalbum.update');
    Route::get('/fotoalbum/lezen/{photoalbum}', PhotoalbumRead::class)->name('photoalbum.read');
    Route::get('/fotoalbum/bestanden/{photoalbum}', PhotoalbumUpload::class)->name('photoalbum.upload');

    /**
     * * Products
     */
    Route::get('/producten', ProductList::class)->name('product.list');
    Route::get('/producten/toevoegen', ProductCreate::class)->name('product.create');
    Route::get('/producten/aanpassen/{product}', ProductUpdate::class)->name('product.update');
    Route::get('/producten/lezen/{product}', ProductRead::class)->name('product.read');
    Route::get('/producten/bestanden/{product}', ProductUpload::class)->name('product.upload');
    Route::get('/producten/categorie/{product}', ProductCategory::class)->name('product.category');

    Route::get('/product/categorieen', ProductcatList::class)->name('productcat.list');
    Route::get('/product/categorieen/boom', ProductcatTree::class)->name('productcat.tree');
    Route::get('/product/categorieen/toevoegen', ProductcatCreate::class)->name('productcat.create');
    Route::get('/product/categorieen/aanpassen/{productcat}', ProductcatUpdate::class)->name('productcat.update');
    Route::get('/product/categorieen/lezen/{productcat}', ProductcatRead::class)->name('productcat.read');
    Route::get('/product/categorieen/bestanden/{productcat}', ProductcatUpload::class)->name('productcat.upload');

    /**
     * * Pages
     */
    Route::get('/tekstpagina', PageList::class)->name('page.list');
    Route::get('/tekstpagina/toevoegen', PageCreate::class)->name('page.create');
    Route::get('/tekstpagina/aanpassen/{page}', PageUpdate::class)->name('page.update');
    Route::get('/tekstpagina/lezen/{page}', PageRead::class)->name('page.read');
    Route::get('/tekstpagina/bestanden/{page}', PageUpload::class)->name('page.upload');

    /**
     * * Staff
     */
    Route::get('/gebruikers', StaffList::class)->name('staff.list');
    Route::get('/gebruikers/toevoegen', StaffCreate::class)->name('staff.create');
    Route::get('/gebruikers/aanpassen/{staff}', StaffUpdate::class)->name('staff.update');
    Route::get('/gebruikers/lezen/{staff}', StaffRead::class)->name('staff.read');

    /**
     * * Routes SEO
     */
    Route::get('/route-seo', RouteseoList::class)->name('routeseo.list');
    Route::get('/route-seo/toevoegen', RouteseoCreate::class)->name('routeseo.create');
    Route::get('/route-seo/aanpassen/{routeseo}', RouteseoUpdate::class)->name('routeseo.update');
    Route::get('/route-seo/lezen/{routeseo}', RouteseoRead::class)->name('routeseo.read');
    Route::get('/route-seo/bestanden/{routeseo}', RouteseoUpload::class)->name('routeseo.upload');

    /**
     * * Users
     */
    Route::get('/klanten', UserList::class)->name('user.list');
    Route::get('/klanten/toevoegen', UserCreate::class)->name('user.create');
    Route::get('/klanten/aanpassen/{user}', UserUpdate::class)->name('user.update');
    Route::get('/klanten/lezen/{user}', UserRead::class)->name('user.read');

    /**
     * * Uploads
     */
    Route::get('/uploads', UploadList::class)->name('upload.list');
    Route::get('/uploads/toevoegen', UploadCreate::class)->name('upload.create');
    Route::get('/uploads/aanpassen/{upload}', UploadUpdate::class)->name('upload.update');
    Route::get('/uploads/lezen/{upload}', UploadRead::class)->name('upload.read');
    Route::get('/uploads/crop/{upload}', UploadCrop::class)->name('upload.crop');
});
