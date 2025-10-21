<?php

namespace Database\Seeders;

use App\Models\PageModel;
use App\Models\PageTranslationModel;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class DefaultPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create "About" page
        $aboutPage = PageModel::create([
            'id' => Uuid::uuid4()->toString(),
            'slug' => 'about',
            'status' => 'published',
        ]);

        PageTranslationModel::create([
            'page_id' => $aboutPage->id,
            'locale' => 'es',
            'title' => 'Sobre mí',
            'content' => '<h2>Sobre mí</h2><p>Esta es la página "Sobre mí". Aquí puedes compartir información sobre ti, tu experiencia, tus intereses y tu historia.</p><p>Edita este contenido desde el panel de administración.</p>',
            'meta_description' => 'Conoce más sobre mí y mi trayectoria profesional',
        ]);

        PageTranslationModel::create([
            'page_id' => $aboutPage->id,
            'locale' => 'en',
            'title' => 'About',
            'content' => '<h2>About</h2><p>This is the "About" page. Here you can share information about yourself, your experience, your interests and your story.</p><p>Edit this content from the admin panel.</p>',
            'meta_description' => 'Learn more about me and my professional background',
        ]);

        // Create "Contact" page
        $contactPage = PageModel::create([
            'id' => Uuid::uuid4()->toString(),
            'slug' => 'contact',
            'status' => 'published',
        ]);

        PageTranslationModel::create([
            'page_id' => $contactPage->id,
            'locale' => 'es',
            'title' => 'Contacto',
            'content' => '<h2>Contacto</h2><p>¿Tienes alguna pregunta o quieres ponerte en contacto conmigo?</p><p><strong>Email:</strong> tu-email@ejemplo.com</p><p><strong>Redes sociales:</strong></p><ul><li>Twitter: @tuusuario</li><li>LinkedIn: tu-perfil</li><li>GitHub: tu-usuario</li></ul><p>Edita esta información desde el panel de administración.</p>',
            'meta_description' => 'Ponte en contacto conmigo a través de email o redes sociales',
        ]);

        PageTranslationModel::create([
            'page_id' => $contactPage->id,
            'locale' => 'en',
            'title' => 'Contact',
            'content' => '<h2>Contact</h2><p>Do you have any questions or want to get in touch with me?</p><p><strong>Email:</strong> your-email@example.com</p><p><strong>Social media:</strong></p><ul><li>Twitter: @youruser</li><li>LinkedIn: your-profile</li><li>GitHub: your-user</li></ul><p>Edit this information from the admin panel.</p>',
            'meta_description' => 'Get in touch with me via email or social media',
        ]);

        $this->command->info('Default pages created successfully!');
        $this->command->info('- About page: /about');
        $this->command->info('- Contact page: /contact');
    }
}
