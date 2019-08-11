import { createLocalVue, mount } from '@vue/test-utils';
import expect from 'expect';
import Navbar from '../../src/resources/js/components/NavbarComponent.vue'
import BootstrapVue from 'bootstrap-vue' //Importing

// create an extended `Vue` constructor
const localVue = createLocalVue();

// install plugins as normal
localVue.use(BootstrapVue);

describe('Navbar', () => {
  // pass the `localVue` to the mount options
  let wrapper = mount(Navbar, {
    localVue
  });

  it('should have loading truck icon', function () {
    expect(wrapper.html()).toContain("<i class=\"fas fa-truck-loading\"></i>");
  });
});