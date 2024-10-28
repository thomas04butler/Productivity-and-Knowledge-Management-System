import './bootstrap';
import 'flowbite';

import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect'
import formData from 'alpinejs-form-data'

window.Alpine = Alpine;

Alpine.plugin(intersect)
Alpine.plugin(formData)

Alpine.start();
