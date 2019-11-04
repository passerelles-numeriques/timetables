export default class Lesson {
  constructor(name = '', id = 0, module = 0, teacher = 0, hours = 0, curriculum = '') {
    this.name = name;
    this.id = id;
    this.module = module;
    this.teacher = teacher;
    this.hours = hours;
    this.curriculum = curriculum;
  }

  equals(lesson) {
    return this.id === lesson.id &&
      this.name === lesson.name &&
      this.module === lesson.module &&
      this.teacher === lesson.teacher &&
      this.hours === lesson.hours &&
      this.curriculum === lesson.curriculum;
  }
}
