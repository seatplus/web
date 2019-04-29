import { mount } from '@vue/test-utils';
import expect from 'expect';
import Example from '../../src/resources/js/components/ExampleComponent.vue'

describe('Example', () => {
  it ('says that it has been mounted', () => {
    let wrapper = mount(Example);

    expect(wrapper.html()).toContain('Example Component');
  })
});